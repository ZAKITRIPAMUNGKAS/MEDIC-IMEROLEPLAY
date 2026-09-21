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
        'sub_role_id',
        'medic_role_id',
        'staff_id',
        'citizen_id',
        'hospital',
        'is_active',
        'profile_image',
        'custom_permissions',
        'custom_salary',
        'status',
        'last_seen_at',
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
            'last_seen_at' => 'datetime',
        ];
    }

    public function role()
    {
        return $this->belongsTo(StaffRole::class, 'role_id');
    }

    /**
     * Sub-jabatan / divisi (IE, PND, MSL, GA, Comdis)
     */
    public function subRole()
    {
        return $this->belongsTo(StaffSubRole::class, 'sub_role_id');
    }

    /**
     * Jabatan Medis / Jenjang Klinis (Trainee, Perawat, Co-Ass, Dokter Umum, Dokter Spesialis)
     */
    public function medicRole()
    {
        return $this->belongsTo(StaffRole::class, 'medic_role_id');
    }

    /**
     * Jabatan Medis efektif:
     * - Jika medic_role_id terisi, gunakan itu (misal Staff Manager yang secara klinis adalah Co-Ass / Dokter).
     * - Jika tidak, dan role_id utama merupakan role medis, gunakan role_id tersebut.
     */
    public function getEffectiveMedicRoleAttribute(): ?StaffRole
    {
        if ($this->medic_role_id) {
            return $this->relationLoaded('medicRole') ? $this->medicRole : $this->medicRole()->first();
        }
        if ($this->role && in_array(strtolower($this->role->name), ['dokter_spesialis', 'dokter_umum', 'co_ass', 'perawat', 'trainee'])) {
            return $this->role;
        }
        return null;
    }

    /**
     * Label tampilan peran lengkap (Manajemen + Divisi + Medis)
     */
    public function getFullRoleTitleAttribute(): string
    {
        $parts = [];
        if ($this->role) {
            $parts[] = $this->role->display_name;
        }
        if ($this->subRole) {
            $parts[] = 'Divisi ' . $this->subRole->short_name;
        }
        $effMedic = $this->effective_medic_role;
        if ($effMedic && (!$this->role || $effMedic->id !== $this->role->id)) {
            $parts[] = 'Medis: ' . $effMedic->display_name;
        }
        return implode(' | ', $parts) ?: 'Staff';
    }

    /**
     * Cek apakah user punya sub-jabatan tertentu (berdasarkan name/slug).
     * Contoh: $user->hasSubRole('pnd')
     */
    public function hasSubRole(string $subRoleName): bool
    {
        return $this->subRole && strtolower($this->subRole->name) === strtolower($subRoleName);
    }

    /**
     * Cek apakah user adalah anggota divisi tertentu (bisa cek beberapa sekaligus).
     * Contoh: $user->isInDivision('pnd', 'ie')
     */
    public function isInDivision(string ...$divisions): bool
    {
        if (!$this->subRole) return false;
        return in_array(strtolower($this->subRole->name), array_map('strtolower', $divisions));
    }

    /**
     * Cek apakah user adalah Executive atau Admin (bisa assign sub-jabatan).
     */
    public function isExecutiveOrAbove(): bool
    {
        if ($this->isAdmin()) return true;
        return (bool) ($this->role && $this->role->level >= 7);
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

    public function evaluationsGiven()
    {
        return $this->hasMany(ManagerEvaluation::class, 'evaluator_id');
    }

    public function evaluationsReceived()
    {
        return $this->hasMany(ManagerEvaluation::class, 'manager_id');
    }

    public function isStaff()
    {
        return !is_null($this->role_id);
    }


    public function isAdmin()
    {
        return $this->role && $this->role->name === 'admin';
    }

    public function isManagerOrAbove(): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return (bool) ($this->role && $this->role->level >= 5);
    }

    /**
     * Check if the user has a specific permission through their role.
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission(string $permission): bool
    {
        // Admin always has all permissions
        if ($this->isAdmin()) {
            return true;
        }

        // Check custom user permissions first
        if (!empty($this->custom_permissions) && in_array($permission, $this->custom_permissions)) {
            return true;
        }

        // Jabatan Manager ke atas (level >= 5: Staff Manager, Manajer, Executive, Admin) dapat melihat Laporan & Keluhan
        if ($permission === 'access_feedback') {
            if ($this->role && $this->role->level >= 5) {
                return true;
            }
        }

        // Dokter Spesialis ke atas (level >= 4) dapat mengelola Jadwal Dokter
        if ($permission === 'manage_doctor_schedules') {
            if ($this->role && $this->role->level >= 4) {
                return true;
            }
        }

        // Check role permissions
        return (bool) ($this->role?->hasPermission($permission) ?? false);
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

        // Surat keterangan oplas (operasi plastik): minimal Co-ass (level 2) ke atas
        if ($formType === 'operasi_plastik') {
            return $userLevel >= 2;
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
        if ($this->hospital === 'roxwood') {
            return true;
        }
        if ($this->hospital === 'alta') {
            return false;
        }
        $name = strtolower($this->name ?? '');
        $staffId = strtolower($this->staff_id ?? '');
        return str_contains($name, 'rh') || str_contains($name, 'roxwood') || str_contains($staffId, 'rh');
    }

    public function isAlta(): bool
    {
        return !$this->isRoxwood();
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

    /**
     * Get private messages sent by this user
     */
    public function sentMessages()
    {
        return $this->hasMany(MemberMessage::class, 'sender_id');
    }

    /**
     * Credit Score anggota
     */
    public function creditScore()
    {
        return $this->hasOne(CreditScore::class);
    }

    /**
     * Ambil saldo credit score (buat baru jika belum ada, default 100)
     */
    public function getCreditBalance(): int
    {
        return CreditScore::getOrCreate($this->id)->balance;
    }

    /**
     * Get private messages received by this user
     */
    public function receivedMessages()
    {
        return $this->hasMany(MemberMessage::class, 'receiver_id');
    }

    // ─── Portal: Cuti ─────────────────────────────────────────────────────────

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    // ─── Portal: Resign ───────────────────────────────────────────────────────

    public function resignationRequests()
    {
        return $this->hasMany(ResignationRequest::class);
    }

    // ─── Portal: Sertifikasi Profil ───────────────────────────────────────────

    public function certifications()
    {
        return $this->hasMany(MemberCertification::class);
    }

    /**
     * Cek apakah user memiliki sertifikat aktif dari divisi/tipe tertentu.
     * Contoh: $user->hasCertification('vehicle_land')
     */
    public function hasCertification(string $type): bool
    {
        return $this->certifications()->where('type', $type)->where('status', 'active')->exists();
    }

    // ─── Portal: Stase ────────────────────────────────────────────────────────

    public function staseApplications()
    {
        return $this->hasMany(StaseApplication::class);
    }

    /**
     * Cek apakah user sudah lulus stase tertentu (ada entry completed di StaseApplication).
     */
    public function hasCompletedStase(string $staseName = null): bool
    {
        $q = $this->staseApplications()->where('passed', true);
        if ($staseName) $q->where('stase_name', 'like', "%{$staseName}%");
        return $q->exists();
    }

    // ─── Portal: Pengajuan Operasi ────────────────────────────────────────────

    public function operationRequests()
    {
        return $this->hasMany(OperationRequest::class);
    }

    // ─── Portal: Kenaikan Jabatan (Promosi) ───────────────────────────────────

    public function promotionApplications()
    {
        return $this->hasMany(PromotionApplication::class);
    }

    /**
     * Hitung jumlah hari aktif sejak joining (berdasarkan created_at).
     */
    public function getDaysActiveSinceJoining(): int
    {
        return (int) \Carbon\Carbon::parse($this->created_at)->diffInDays(now());
    }

    /**
     * Hitung jumlah hari aktif sebagai jabatan tertentu (berdasarkan log kenaikan jabatan terakhir).
     * Fallback ke days since joining jika tidak ada data.
     */
    public function getDaysInCurrentRole(): int
    {
        // Gunakan created_at sebagai fallback
        $lastPromotion = $this->promotionApplications()
            ->where('status', 'approved')
            ->where('target_role_id', $this->role_id)
            ->latest('pnd_reviewed_at')
            ->first();

        if ($lastPromotion && $lastPromotion->pnd_reviewed_at) {
            return (int) \Carbon\Carbon::parse($lastPromotion->pnd_reviewed_at)->diffInDays(now());
        }

        return $this->getDaysActiveSinceJoining();
    }

    /**
     * Hitung total jam on-duty (from attendances).
     */
    public function getTotalDutyHours(): float
    {
        $seconds = $this->getTotalDutySeconds();
        return round($seconds / 3600, 2);
    }

    /**
     * Hitung jumlah tindakan operasi sebagai DPJP (minor).
     */
    public function getDpjpMinorOperationCount(): int
    {
        return \App\Models\OperationRecord::where('dpjp_id', $this->id)
            ->where('jenis_operasi', 'Operasi Minor')
            ->where('hospital', $this->hospital ?? 'alta')
            ->count();
    }

    /**
     * Hitung jumlah tindakan operasi sebagai asisten (minor).
     */
    public function getAssistantMinorOperationCount(): int
    {
        return \App\Models\OperationRecord::whereHas('members', fn($q) => $q->where('user_id', $this->id))
            ->where('jenis_operasi', 'Operasi Minor')
            ->where('hospital', $this->hospital ?? 'alta')
            ->count();
    }

    /**
     * Hitung jumlah tindakan operasi sebagai asisten (mayor).
     */
    public function getAssistantMayorOperationCount(): int
    {
        return \App\Models\OperationRecord::whereHas('members', fn($q) => $q->where('user_id', $this->id))
            ->where('jenis_operasi', 'Operasi Mayor')
            ->where('hospital', $this->hospital ?? 'alta')
            ->count();
    }

    /**
     * Hitung jumlah tindakan operasi sebagai DPJP (mayor).
     */
    public function getDpjpMayorOperationCount(): int
    {
        return \App\Models\OperationRecord::where('dpjp_id', $this->id)
            ->where('jenis_operasi', 'Operasi Mayor')
            ->where('hospital', $this->hospital ?? 'alta')
            ->count();
    }

    /**
     * Build checklist persyaratan kenaikan jabatan ke target role tertentu.
     * Return array: [['key'=>, 'label'=>, 'met'=>bool], ...]
     */
    public function buildPromotionChecklist(\App\Models\StaffRole $targetRole): array
    {
        $targetName   = strtolower($targetRole->name);
        $currentMedic = $this->effective_medic_role ?? $this->role;
        $currentName  = strtolower($currentMedic?->name ?? '');
        $creditScore  = $this->getCreditBalance();
        $checklist    = [];

        // ── Trainee ke jenjang awal ───────────────────────────────────────────
        if ($currentName === 'trainee') {
            $checklist[] = [
                'key'   => 'credit_score_80',
                'label' => 'Credit Score minimal 80 poin (saat ini: ' . $creditScore . ')',
                'met'   => $creditScore >= 80,
            ];
            $trainingDays = $this->getDaysActiveSinceJoining();
            $checklist[] = [
                'key'   => 'training_days_7',
                'label' => 'Masa training minimal 7 hari (saat ini: ' . $trainingDays . ' hari)',
                'met'   => $trainingDays >= 7,
            ];
            $dutyHours = $this->getTotalDutyHours();
            $checklist[] = [
                'key'   => 'duty_hours_15',
                'label' => 'Jam terbang on-duty minimal 15 jam (saat ini: ' . $dutyHours . ' jam)',
                'met'   => $dutyHours >= 15,
            ];
            $hasVehicle = $this->hasCertification('vehicle_land') || $this->hasCertification('vehicle_heli');
            $checklist[] = [
                'key'   => 'vehicle_cert',
                'label' => 'Memiliki sertifikat kendaraan (GA)',
                'met'   => $hasVehicle,
            ];
            return $checklist;
        }

        // ── Perawat & Co-ass ke tingkat berikutnya ────────────────────────────
        if (in_array($currentName, ['perawat', 'co_ass'])) {
            $checklist[] = [
                'key'   => 'credit_score_80',
                'label' => 'Credit Score minimal 80 poin (saat ini: ' . $creditScore . ')',
                'met'   => $creditScore >= 80,
            ];
            $checklist[] = [
                'key'   => 'operation_cert',
                'label' => 'Memiliki sertifikat operasi (PND)',
                'met'   => $this->hasCertification('operation_cert'),
            ];
            $assistantMinor = $this->getAssistantMinorOperationCount();
            $checklist[] = [
                'key'   => 'assistant_minor_5',
                'label' => 'Asisten Operasi Minor minimal 5x (saat ini: ' . $assistantMinor . 'x)',
                'met'   => $assistantMinor >= 5,
            ];
            $assistantMayor = $this->getAssistantMayorOperationCount();
            $checklist[] = [
                'key'   => 'assistant_mayor_1',
                'label' => 'Pernah menjadi Asisten Operasi Mayor (saat ini: ' . $assistantMayor . 'x)',
                'met'   => $assistantMayor >= 1,
            ];
            $checklist[] = [
                'key'   => 'medical_contract',
                'label' => 'Memiliki Surat Perjanjian Kontrak Medis (IE)',
                'met'   => $this->hasCertification('medical_contract'),
            ];
            return $checklist;
        }

        // ── Co-ass ke Dokter Umum ─────────────────────────────────────────────
        if ($currentName === 'co_ass' && in_array($targetName, ['dokter_umum', 'dokter umum'])) {
            // (dihandle blok di atas, tapi kita override khusus jika target = dokter_umum)
            // Reset dan rebuild
            $checklist = [];
            $checklist[] = [
                'key'   => 'credit_score_80',
                'label' => 'Credit Score minimal 80 poin (saat ini: ' . $creditScore . ')',
                'met'   => $creditScore >= 80,
            ];
            $checklist[] = [
                'key'   => 'operation_cert',
                'label' => 'Memiliki sertifikat operasi (PND)',
                'met'   => $this->hasCertification('operation_cert'),
            ];
            $checklist[] = [
                'key'   => 'medical_contract',
                'label' => 'Memiliki Surat Perjanjian Kontrak Medis (IE)',
                'met'   => $this->hasCertification('medical_contract'),
            ];
            $dpjpMinor = $this->getDpjpMinorOperationCount();
            $checklist[] = [
                'key'   => 'dpjp_minor_5',
                'label' => 'DPJP Operasi Minor minimal 5x (saat ini: ' . $dpjpMinor . 'x)',
                'met'   => $dpjpMinor >= 5,
            ];
            $assistantMayor = $this->getAssistantMayorOperationCount();
            $checklist[] = [
                'key'   => 'assistant_mayor_5',
                'label' => 'Asisten Operasi Mayor minimal 5x (saat ini: ' . $assistantMayor . 'x)',
                'met'   => $assistantMayor >= 5,
            ];
            // Surat rekomendasi konsulen diupload manual — cek file
            $checklist[] = [
                'key'   => 'recommendation_letter',
                'label' => '2 surat rekomendasi dari Konsulen stase (dilampirkan saat submit)',
                'met'   => false, // selalu false di sini, dicek saat submit
            ];
            return $checklist;
        }

        // ── Dokter Umum ke Dokter Spesialis ───────────────────────────────────
        if (in_array($currentName, ['dokter_umum', 'dokter umum'])) {
            $checklist[] = [
                'key'   => 'credit_score_85',
                'label' => 'Credit Score minimal 85 poin (saat ini: ' . $creditScore . ')',
                'met'   => $creditScore >= 85,
            ];
            $daysInRole = $this->getDaysInCurrentRole();
            $checklist[] = [
                'key'   => 'role_active_20_days',
                'label' => 'Masa aktif Dokter Umum minimal 20 hari (saat ini: ' . $daysInRole . ' hari)',
                'met'   => $daysInRole >= 20,
            ];
            // Terdaftar aktif dalam Dokter Residen — cek stase aktif / completed
            $isResiden = $this->staseApplications()
                ->where('passed', true)
                ->exists();
            $checklist[] = [
                'key'   => 'residen_active',
                'label' => 'Terdaftar aktif dalam program Dokter Residen (lulus minimal 1 stase)',
                'met'   => $isResiden,
            ];
            $checklist[] = [
                'key'   => 'case_study_file',
                'label' => 'Laporan Studi Kasus spesialisasi (dilampirkan saat submit)',
                'met'   => false, // dicek saat submit
            ];
            return $checklist;
        }

        return $checklist;
    }

    // ─── Existing: Online Status & Duty Seconds ───────────────────────────────

    /**
     * Check if user is currently online on the dashboard
     */
    public function isOnline(): bool
    {
        if ($this->isClockedIn()) {
            return true;
        }
        return $this->last_seen_at && $this->last_seen_at->greaterThanOrEqualTo(now()->subMinutes(5));
    }

    /**
     * Get total duty duration in seconds (all-time)
     */
    public function getTotalDutySeconds(): int
    {
        return $this->attendances()->whereNotNull('clock_out')->sum('session_duration') ?? 0;
    }

    /**
     * Get total duty duration formatted as human readable (all-time)
     */
    public function getTotalDutyHoursFormatted(): string
    {
        return \App\Helpers\TimeHelper::getHumanReadableDuration($this->getTotalDutySeconds());
    }
}
