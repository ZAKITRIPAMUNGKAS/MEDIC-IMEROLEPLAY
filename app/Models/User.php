<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'staff_id',
        'citizen_id',
        'hospital',
        'is_active',
        'profile_image',
        'custom_permissions',
        'custom_salary',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'custom_permissions' => 'array',
        ];
    }

    public function role()
    {
        return $this->belongsTo(StaffRole::class, 'role_id');
    }

    public function organizationalPositions()
    {
        return $this->hasMany(OrganizationalPosition::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function processedForms()
    {
        return $this->hasMany(MedicalForm::class, 'processed_by');
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function paidPayrolls()
    {
        return $this->hasMany(Payroll::class, 'paid_by');
    }

    public function payrollNotifications()
    {
        return $this->hasMany(PayrollNotification::class);
    }

    public function salaryReimbursements()
    {
        return $this->hasMany(SalaryReimbursement::class, 'manager_id');
    }

    public function processedReimbursements()
    {
        return $this->hasMany(SalaryReimbursement::class, 'reimbursed_by');
    }

    public function isStaff()
    {
        return !is_null($this->role_id);
    }


    public function isAdmin()
    {
        return $this->role && $this->role->name === 'admin';
    }

    /**
     * Check if the user has a specific permission through their role.
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission(string $permission): bool
    {
        // Check custom user permissions first
        if (!empty($this->custom_permissions) && in_array($permission, $this->custom_permissions)) {
            return true;
        }

        // Check role permissions
        return $this->role?->hasPermission($permission) ?? false;
    }

    /**
     * Get the profile image URL.
     * Note: File existence check is handled by frontend onerror handler for performance.
     *
     * @return string
     */
    public function getProfileImageUrlAttribute()
    {
        if ($this->profile_image) {
            // Check if it's a storage path or public path
            if (str_starts_with($this->profile_image, 'uploads/')) {
                // Direct public path
                return asset($this->profile_image);
            } else {
                // Storage path
                return asset('storage/' . $this->profile_image);
            }
        }

        return asset('profile.jpg');
    }

    /**
     * Get profile image URL with fallback.
     * Returns default image URL if profile_image is null or empty.
     *
     * @return string
     */
    public function getProfileImageUrlWithFallbackAttribute(): string
    {
        return $this->profile_image_url;
    }

    /**
     * Get onerror handler for profile images.
     * Returns JavaScript code to handle image loading errors.
     *
     * @return string
     */
    public function getProfileImageOnErrorAttribute(): string
    {
        $defaultImage = asset('profile.jpg');
        return "this.onerror=null;this.src='{$defaultImage}';";
    }

    /**
     * Check if profile image file actually exists.
     * Use this method for database cleanup commands, not for regular requests.
     * Checks multiple possible paths to ensure file exists.
     *
     * @return bool
     */
    public function hasValidProfileImage(): bool
    {
        if (!$this->profile_image) {
            return false;
        }

        $pathsToCheck = [];

        // Determine which paths to check based on profile_image format
        if (str_starts_with($this->profile_image, 'uploads/')) {
            // Direct public path: uploads/profile-images/file.jpg
            $pathsToCheck[] = public_path($this->profile_image);
        } elseif (str_starts_with($this->profile_image, 'profile-images/')) {
            // Path starting with profile-images/: profile-images/file.jpg
            // Check in public/uploads/profile-images/file.jpg
            $pathsToCheck[] = public_path('uploads/' . $this->profile_image);
            // Also check in storage/app/public/profile-images/file.jpg
            $pathsToCheck[] = storage_path('app/public/' . $this->profile_image);
        } else {
            // Storage path or just filename
            // Check storage path first
            $pathsToCheck[] = storage_path('app/public/' . $this->profile_image);
            // Check public/uploads/profile-images if it's just a filename
            if (!str_contains($this->profile_image, '/')) {
                $pathsToCheck[] = public_path('uploads/profile-images/' . $this->profile_image);
            } else {
                // Check public/uploads/ for the path
                $pathsToCheck[] = public_path('uploads/' . $this->profile_image);
            }
        }

        // Check all possible paths
        foreach ($pathsToCheck as $filePath) {
            if (file_exists($filePath) && is_file($filePath)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Scope untuk mengurutkan staff berdasarkan level jabatan (tertinggi ke terendah)
     */
    public function scopeOrderByRoleLevel($query, $direction = 'desc')
    {
        return $query->join('staff_roles', 'users.role_id', '=', 'staff_roles.id')
            ->orderBy('staff_roles.level', $direction)
            ->orderBy('users.name', 'asc')
            ->select('users.*');
    }

    /**
     * Scope untuk mengecualikan admin dari hasil query
     */
    public function scopeExcludeAdmin($query)
    {
        return $query->whereHas('role', function ($q) {
            $q->where('name', '!=', 'admin');
        });
    }

    /**
     * Check if user is currently clocked in (has active attendance session)
     *
     * @return bool
     */
    public function isClockedIn(): bool
    {
        return Attendance::getAnyActiveSession($this->id) !== null;
    }

    /**
     * Check if user can approve a specific form type based on role level
     *
     * @param string $formType
     * @return bool
     */
    public function canApproveForm(string $formType): bool
    {
        $userLevel = $this->role->level ?? 0;

        // Surat kesehatan dan surat psikolog: minimal Co-ass (level 2) ke atas
        if (in_array($formType, ['surat_kesehatan', 'tes_psikologi', 'surat_psikolog'])) {
            return $userLevel >= 2;
        }

        // Surat keterangan oplas (operasi plastik): minimal dokter umum (level 3) ke atas
        if ($formType === 'operasi_plastik') {
            return $userLevel >= 3;
        }

        // Untuk form lain, semua user dengan role bisa approve (default behavior)
        return true;
    }

    /**
     * Check if user can reply to live chat (has reply_livechat permission)
     *
     * @return bool
     */
    public function canReplyChat(): bool
    {
        return $this->hasPermission('access_live_chat');
    }

    /**
     * Check if user belongs to Roxwood Hospital
     *
     * @return bool
     */
    public function isRoxwood(): bool
    {
        return $this->hospital === 'roxwood';
    }

    /**
     * Check if user belongs to Alta Hospital
     *
     * @return bool
     */
    public function isAlta(): bool
    {
        return $this->hospital === 'alta' || $this->hospital === null;
    }

    /**
     * Scope untuk memfilter user Roxwood Hospital
     */
    public function scopeRoxwood($query)
    {
        return $query->where('hospital', 'roxwood');
    }

    /**
     * Scope untuk memfilter user Alta Hospital
     */
    public function scopeAlta($query)
    {
        return $query->where(function ($q) {
            $q->where('hospital', 'alta')
                ->orWhereNull('hospital');
        });
    }

    /**
     * Get user's hospital (alta or roxwood)
     *
     * @return string
     */
    public function getHospital(): string
    {
        return $this->hospital ?? 'alta';
    }

    /**
     * Get all rename logs for this user
     */
    public function renameLogs()
    {
        return $this->hasMany(UserRenameLog::class, 'user_id');
    }
}
