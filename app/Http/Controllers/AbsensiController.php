<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Services\AttendanceIntegrationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    /**
     * API endpoint untuk menerima data absensi dari FiveM
     * POST /api/absensi
     */
    public function store(Request $request): JsonResponse
    {
        // Validasi input dengan sanitasi
        $validator = Validator::make($request->all(), [
            'player_id' => 'required|string|max:255|regex:/^[a-zA-Z0-9_:]+$/',
            'player_name' => 'required|string|max:255|regex:/^[a-zA-Z0-9\s_-]+$/',
            'clock_in' => 'required|date|before_or_equal:now',
            'clock_out' => 'nullable|date|after:clock_in|before_or_equal:now',
            'time_on_duty' => 'nullable|string|regex:/^\d{2}:\d{2}:\d{2}$/'
        ], [
            'player_id.required' => 'Player ID wajib diisi',
            'player_id.regex' => 'Player ID hanya boleh berisi huruf, angka, underscore, dan colon',
            'player_name.required' => 'Player Name wajib diisi',
            'player_name.regex' => 'Player Name hanya boleh berisi huruf, angka, spasi, underscore, dan dash',
            'clock_in.required' => 'Clock In wajib diisi',
            'clock_in.date' => 'Format Clock In tidak valid',
            'clock_in.before_or_equal' => 'Clock In tidak boleh di masa depan',
            'clock_out.date' => 'Format Clock Out tidak valid',
            'clock_out.after' => 'Clock Out harus setelah Clock In',
            'clock_out.before_or_equal' => 'Clock Out tidak boleh di masa depan',
            'time_on_duty.regex' => 'Format Time On Duty harus HH:MM:SS'
        ]);

        if ($validator->fails()) {
            \Log::warning('Absensi API validation failed', [
                'errors' => $validator->errors()->toArray(),
                'ip' => $request->ip()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Sanitasi dan validasi data
            $data = [
                'player_id' => trim(strip_tags($request->player_id)),
                'player_name' => trim(strip_tags($request->player_name)),
                'clock_in' => $request->clock_in,
                'clock_out' => $request->clock_out,
                'time_on_duty' => $request->time_on_duty
            ];
            
            // Additional validation: Check duration if both clock_in and clock_out exist
            if ($data['clock_out']) {
                $clockIn = Carbon::parse($data['clock_in']);
                $clockOut = Carbon::parse($data['clock_out']);
                $durationMinutes = $clockIn->diffInMinutes($clockOut);
                
                // Validate: minimum duration 1 minute
                if ($durationMinutes < 1) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Durasi terlalu pendek (minimum 1 menit)',
                        'error_code' => 'DURATION_TOO_SHORT'
                    ], 400);
                }
                
                // Validate: maximum duration 24 hours
                if ($durationMinutes > 1440) {
                    \Log::warning('Absensi duration exceeds 24 hours', [
                        'player_id' => $data['player_id'],
                        'duration_minutes' => $durationMinutes,
                        'clock_in' => $data['clock_in'],
                        'clock_out' => $data['clock_out']
                    ]);
                }
            }
            
            // Cek apakah player sudah clock in tapi belum clock out
            if (!$data['clock_out'] && Absensi::isPlayerActive($data['player_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Player sudah melakukan clock in. Silakan clock out terlebih dahulu.',
                    'error_code' => 'DUPLICATE_CLOCK_IN'
                ], 400);
            }
            
            // Use database transaction
            \DB::beginTransaction();
            
            try {
                // Gunakan service integrasi untuk menangani konflik dengan sistem manual
                $integrationService = new AttendanceIntegrationService();
                $result = $integrationService->integrateAttendanceData(
                    $data['player_id'],
                    $data['player_name'],
                    $data['clock_in'],
                    $data['clock_out'] ?? null,
                    $data['time_on_duty'] ?? null
                );
                
                if ($result['success']) {
                    \DB::commit();
                    
                    \Log::info('Absensi API successful', [
                        'player_id' => $data['player_id'],
                        'has_clock_out' => !empty($data['clock_out']),
                        'priority' => $result['priority'] ?? 'automatic'
                    ]);
                    
                    return response()->json([
                        'success' => true,
                        'message' => $result['message'],
                        'data' => $result['data'] ?? null,
                        'priority' => $result['priority'] ?? 'automatic',
                        'conflict_note' => $result['conflict_note'] ?? null
                    ], 201);
                } else {
                    \DB::rollBack();
                    
                    return response()->json([
                        'success' => false,
                        'message' => $result['message']
                    ], 400);
                }
            } catch (\Exception $e) {
                \DB::rollBack();
                throw $e;
            }
            
        } catch (\Exception $e) {
            \Log::error('Absensi API error', [
                'player_id' => $data['player_id'] ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * API endpoint untuk mendapatkan data absensi
     * GET /api/absensi
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Absensi::query();
            
            // Filter berdasarkan player_id jika ada
            if ($request->has('player_id')) {
                $query->byPlayer($request->player_id);
            }
            
            // Filter berdasarkan tanggal jika ada
            if ($request->has('date_from')) {
                $query->whereDate('clock_in', '>=', $request->date_from);
            }
            
            if ($request->has('date_to')) {
                $query->whereDate('clock_in', '<=', $request->date_to);
            }
            
            // Pagination
            $perPage = $request->get('per_page', 15);
            $absensi = $query->orderBy('created_at', 'desc')->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'data' => $absensi
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * API endpoint untuk mendapatkan status absensi player
     * GET /api/absensi/status/{player_id}
     */
    public function status($playerId): JsonResponse
    {
        try {
            $isActive = Absensi::isPlayerActive($playerId);
            $lastAbsensi = Absensi::byPlayer($playerId)->orderBy('created_at', 'desc')->first();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'player_id' => $playerId,
                    'is_active' => $isActive,
                    'last_absensi' => $lastAbsensi
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * API endpoint untuk monitoring real-time (siapa yang on duty)
     * GET /api/absensi/on-duty
     */
    public function onDuty(Request $request): JsonResponse
    {
        try {
            $onDutyPlayers = Absensi::active()
                ->with(['player_id', 'player_name', 'clock_in'])
                ->orderBy('clock_in', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total_on_duty' => $onDutyPlayers->count(),
                    'players' => $onDutyPlayers->map(function ($absensi) {
                        return [
                            'player_id' => $absensi->player_id,
                            'player_name' => $absensi->player_name,
                            'clock_in' => $absensi->clock_in,
                            'duration' => $absensi->clock_in->diffForHumans(now(), true)
                        ];
                    })
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * API endpoint untuk rekap jam kerja player
     * GET /api/absensi/report/{player_id}
     */
    public function report(Request $request, $playerId): JsonResponse
    {
        try {
            $period = $request->get('period', 'week'); // week, month, year
            $dateFrom = $request->get('date_from');
            $dateTo = $request->get('date_to');
            
            $query = Absensi::byPlayer($playerId)->whereNotNull('clock_out');
            
            if ($dateFrom && $dateTo) {
                $query->whereBetween('clock_in', [$dateFrom, $dateTo]);
            } else {
                switch ($period) {
                    case 'week':
                        $query->where('clock_in', '>=', now()->startOfWeek());
                        break;
                    case 'month':
                        $query->where('clock_in', '>=', now()->startOfMonth());
                        break;
                    case 'year':
                        $query->where('clock_in', '>=', now()->startOfYear());
                        break;
                }
            }
            
            $absensi = $query->orderBy('clock_in', 'desc')->get();
            
            // Hitung total jam kerja
            $totalHours = 0;
            $totalMinutes = 0;
            $totalSeconds = 0;
            
            foreach ($absensi as $record) {
                if ($record->time_on_duty) {
                    $timeParts = explode(':', $record->time_on_duty);
                    $totalHours += (int)$timeParts[0];
                    $totalMinutes += (int)$timeParts[1];
                    $totalSeconds += (int)$timeParts[2];
                }
            }
            
            // Normalize time
            $totalMinutes += floor($totalSeconds / 60);
            $totalSeconds = $totalSeconds % 60;
            $totalHours += floor($totalMinutes / 60);
            $totalMinutes = $totalMinutes % 60;
            
            $totalWorkTime = sprintf('%02d:%02d:%02d', $totalHours, $totalMinutes, $totalSeconds);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'player_id' => $playerId,
                    'period' => $period,
                    'total_work_days' => $absensi->count(),
                    'total_work_time' => $totalWorkTime,
                    'total_hours' => $totalHours,
                    'total_minutes' => $totalMinutes,
                    'records' => $absensi->map(function ($record) {
                        return [
                            'id' => $record->id,
                            'clock_in' => $record->clock_in,
                            'clock_out' => $record->clock_out,
                            'time_on_duty' => $record->time_on_duty,
                            'date' => $record->clock_in->format('Y-m-d')
                        ];
                    })
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }
}
