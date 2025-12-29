<?php

namespace App\Services;

use App\Models\MedicalForm;
use App\Models\Attendance;
use App\Models\User;
use App\Helpers\AttendanceHelper;
use App\Helpers\PayrollHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardService
{
    /**
     * Get dashboard data for staff
     *
     * @param User $user
     * @return array
     */
    public function getStaffDashboardData(User $user): array
    {
        $attendanceData = $this->getAttendanceData($user);
        $heatmapData = $this->getHeatmapData($user);
        
        return [
            'leaderboard' => $this->getWeeklyLeaderboard(),
            'recentForms' => $this->getRecentForms(),
            'recentAppointments' => $this->getRecentAppointments(),
            'stats' => $this->getFormStatistics(),
            'heatmapData' => $heatmapData,
            'heatmapStats' => $heatmapData['stats'],
            'today_sessions' => $attendanceData['today_sessions'],
            'active_session' => $attendanceData['active_session'],
            'any_active_session' => $attendanceData['any_active_session'],
            'today_total_hours' => $attendanceData['today_total_hours'],
            'weekly_stats' => $attendanceData['weekly_stats']
        ];
    }

    /**
     * Get weekly leaderboard data
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWeeklyLeaderboard()
    {
        return AttendanceHelper::getWeeklyLeaderboard(10);
    }

    /**
     * Get recent forms (non-appointment)
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentForms()
    {
        return MedicalForm::with(['processedBy.role'])
            ->whereNotIn('form_type', $this->getAppointmentTypes())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get recent appointments
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentAppointments()
    {
        return MedicalForm::with(['processedBy.role'])
            ->whereIn('form_type', $this->getAppointmentTypes())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get form statistics
     *
     * @return array
     */
    public function getFormStatistics(): array
    {
        return [
            'pending_forms' => MedicalForm::pending()->count(),
            'approved_forms' => MedicalForm::approved()->count(),
            'rejected_forms' => MedicalForm::rejected()->count(),
            'total_forms_today' => MedicalForm::whereDate('created_at', today())->count(),
        ];
    }

    /**
     * Get attendance data for user
     *
     * @param User $user
     * @return array
     */
    public function getAttendanceData(User $user): array
    {
        try {
            \Log::info('Getting attendance data for user', ['user_id' => $user->id]);
            
            $todaySessions = Attendance::getTodaySessions($user->id);
            \Log::info('Today sessions count', ['count' => $todaySessions->count()]);
            
            $activeSession = Attendance::getActiveSession($user->id, today());
            \Log::info('Active session', ['active_session' => $activeSession ? $activeSession->id : 'null']);
            
            $anyActiveSession = Attendance::getAnyActiveSession($user->id);
            \Log::info('Any active session', ['any_active_session' => $anyActiveSession ? $anyActiveSession->id : 'null']);
            
            $todayTotalHours = Attendance::getDailyTotalHours($user->id, today());
            \Log::info('Today total hours', ['hours' => $todayTotalHours]);
            
            $weeklyStats = AttendanceHelper::getUserWeeklyStats($user->id);
            \Log::info('Weekly stats', ['weekly_stats' => $weeklyStats ? 'exists' : 'null']);

            $result = [
                'today_sessions' => $todaySessions,
                'active_session' => $activeSession,
                'any_active_session' => $anyActiveSession,
                'today_total_hours' => $todayTotalHours,
                'weekly_stats' => $weeklyStats
            ];
            
            \Log::info('Attendance data result keys', ['keys' => array_keys($result)]);
            
            return $result;
        } catch (\Exception $e) {
            \Log::error('Error in getAttendanceData', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return empty data structure to prevent view errors
            return [
                'today_sessions' => collect(),
                'active_session' => null,
                'any_active_session' => null,
                'today_total_hours' => 0,
                'weekly_stats' => null
            ];
        }
    }

    /**
     * Get heatmap data for user
     *
     * @param User $user
     * @param int|null $year
     * @return array
     */
    public function getHeatmapData(User $user, ?int $year = null): array
    {
        $year = $year ?? now()->year;
        
        // Basic heatmap data structure
        return [
            'year' => $year,
            'months' => [],
            'daily_data' => [],
            'stats' => [
                'total_contributions' => 0
            ]
        ];
    }

    /**
     * Get forms with filters
     *
     * @param Request $request
     * @return array
     */
    public function getFilteredForms(Request $request): array
    {
        $query = MedicalForm::with(['processedBy.role']);
        
        // Apply filters
        $this->applyFormFilters($query, $request);
        
        // Get paginated results
        $forms = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Get statistics
        $stats = $this->getFormStatistics();
        
        return [
            'forms' => $forms,
            'stats' => $stats
        ];
    }

    /**
     * Apply form filters to query
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param Request $request
     * @return void
     */
    private function applyFormFilters($query, Request $request): void
    {
        // Search filter
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('character_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        
        // Type filter
        if ($request->filled('type')) {
            $type = $request->get('type');
            if ($type === 'janji_temu') {
                $query->whereIn('form_type', $this->getAppointmentTypes());
            } else {
                $query->where('form_type', $type);
            }
        }
        
        // Category filter
        if ($request->filled('category')) {
            $category = $request->get('category');
            switch ($category) {
                case 'janji_temu':
                    $query->whereIn('form_type', $this->getAppointmentTypes());
                    break;
                case 'konsultasi':
                    $query->whereIn('form_type', $this->getConsultationTypes());
                    break;
                case 'pemeriksaan':
                    $query->whereIn('form_type', $this->getExaminationTypes());
                    break;
                case 'karakter_kill':
                    $query->whereIn('form_type', $this->getCharacterKillTypes());
                    break;
            }
        }
    }

    /**
     * Get appointment form types
     *
     * @return array
     */
    private function getAppointmentTypes(): array
    {
        return [
            'penyakit_dalam',
            'spesialis_anak',
            'spesialis_bedah',
            'spesialis_mata',
            'spesialis_saraf',
            'spesialis_urologi',
            'spesialis_tht',
            'spesialis_ortopedi'
        ];
    }

    /**
     * Get consultation form types
     *
     * @return array
     */
    private function getConsultationTypes(): array
    {
        return [
            'konsultasi_medis',
            'laporan_kecelakaan',
            'permintaan_ambulans'
        ];
    }

    /**
     * Get examination form types
     *
     * @return array
     */
    private function getExaminationTypes(): array
    {
        return [
            'surat_kesehatan',
            'operasi_plastik',
            'tes_psikologi',
            'surat_psikolog'
        ];
    }

    /**
     * Get character kill form types
     *
     * @return array
     */
    private function getCharacterKillTypes(): array
    {
        return [
            'pendaftaran_karakter'
        ];
    }

    /**
     * Get reports data
     *
     * @return array
     */
    public function getReportsData(): array
    {
        return [
            'leaderboard_data' => $this->getWeeklyLeaderboard(),
            'form_stats' => $this->getDetailedFormStatistics()
        ];
    }

    /**
     * Get detailed form statistics
     *
     * @return array
     */
    private function getDetailedFormStatistics(): array
    {
        return [
            'daily' => MedicalForm::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays(7))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'by_type' => MedicalForm::selectRaw('form_type, COUNT(*) as count')
                ->groupBy('form_type')
                ->get(),
            'by_status' => MedicalForm::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get()
        ];
    }
}
