<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;
use App\Models\User;
use App\Jobs\SendDiscordWebhookJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class CheckExpiredDutySessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:check-expired-sessions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and auto checkout expired duty sessions with scheduled timer';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired duty sessions...');

        try {
            // Cari semua session aktif yang scheduled_end_time <= now()
            $now = Carbon::now('Asia/Jakarta');
            
            $expiredSessions = Attendance::where('is_active', true)
                ->whereNotNull('scheduled_duty_minutes')
                ->whereNotNull('scheduled_end_time')
                ->where('scheduled_end_time', '<=', $now)
                ->get();

            $successCount = 0;
            $failCount = 0;
            $overdueAlerts = [];

            if ($expiredSessions->isNotEmpty()) {
                $this->info("Found {$expiredSessions->count()} expired session(s). Processing...");

                foreach ($expiredSessions as $attendance) {
                    // Deteksi apakah sesi sudah jauh melampaui scheduled_end_time (tanda cron tidak jalan)
                    $overdueMinutes = 0;
                    if ($attendance->scheduled_end_time) {
                        $scheduledEnd = Carbon::parse($attendance->scheduled_end_time)->setTimezone('Asia/Jakarta');
                        $overdueMinutes = (int) $scheduledEnd->diffInMinutes($now);
                        
                        // Alert jika overdue lebih dari 10 menit (scheduler harusnya jalan setiap menit)
                        if ($overdueMinutes > 10) {
                            $overdueAlerts[] = [
                                'attendance_id'    => $attendance->id,
                                'user_id'          => $attendance->user_id,
                                'scheduled_end'    => $scheduledEnd->toDateTimeString(),
                                'overdue_minutes'  => $overdueMinutes,
                                'now'              => $now->toDateTimeString(),
                            ];

                            Log::alert('[AUTO-CHECKOUT OVERDUE] Scheduled session was not closed on time — cron may not be running!', [
                                'attendance_id'   => $attendance->id,
                                'user_id'         => $attendance->user_id,
                                'scheduled_end'   => $scheduledEnd->toDateTimeString(),
                                'overdue_minutes' => $overdueMinutes,
                                'now'             => $now->toDateTimeString(),
                            ]);

                            $this->warn("[OVERDUE] Session ID {$attendance->id} was {$overdueMinutes} minute(s) past its scheduled end time! (Cron may not be running)");
                        }
                    }

                    try {
                        DB::beginTransaction();

                        // Auto checkout menggunakan scheduled_duty_minutes sebagai durasi
                        $result = $attendance->autoCloseSession();

                        if ($result) {
                            DB::commit();
                            $successCount++;

                            $this->info("Auto checkout successful for attendance ID: {$attendance->id} (User: {$attendance->user_id}, Scheduled: {$attendance->scheduled_duty_minutes} min, Overdue: {$overdueMinutes} min)");

                            Log::info('Auto checkout completed via command', [
                                'attendance_id'   => $attendance->id,
                                'user_id'         => $attendance->user_id,
                                'scheduled_minutes' => $attendance->scheduled_duty_minutes,
                                'overdue_minutes' => $overdueMinutes,
                                'command'         => 'attendance:check-expired-sessions'
                            ]);
                        } else {
                            DB::rollBack();
                            $failCount++;
                            $this->error("Failed to auto checkout attendance ID: {$attendance->id}");

                            Log::error('[AUTO-CHECKOUT FAILED] autoCloseSession() returned false', [
                                'attendance_id'   => $attendance->id,
                                'user_id'         => $attendance->user_id,
                                'is_active'       => $attendance->is_active,
                                'scheduled_minutes' => $attendance->scheduled_duty_minutes,
                                'scheduled_end_time' => $attendance->scheduled_end_time,
                                'command'         => 'attendance:check-expired-sessions'
                            ]);
                        }
                    } catch (\Exception $e) {
                        DB::rollBack();
                        $failCount++;
                        
                        $this->error("Exception while processing attendance ID: {$attendance->id} - {$e->getMessage()}");

                        Log::error('[AUTO-CHECKOUT EXCEPTION] Exception during session close', [
                            'attendance_id' => $attendance->id,
                            'user_id'       => $attendance->user_id,
                            'error'         => $e->getMessage(),
                            'trace'         => $e->getTraceAsString()
                        ]);
                    }
                }

                // Kirim alert Discord jika ada sesi yang terlambat di-close (indikasi cron mati)
                if (!empty($overdueAlerts)) {
                    $this->sendOverdueAlert($overdueAlerts, $now);
                }

            } else {
                $this->info('No expired scheduled sessions found.');
            }

            // === ZOMBIE SESSION CLEANUP ===
            // Close FiveM sessions that have been active for more than 24 hours without clock-out
            // These are likely stuck due to server crash, player disconnect, or script error
            $zombieThreshold = $now->copy()->subHours(24);
            
            $zombieSessions = Attendance::where('is_active', true)
                ->where('source', 'fivem')
                ->whereNull('scheduled_end_time') // FiveM sessions don't have scheduled end time
                ->where('clock_in', '<', $zombieThreshold)
                ->get();

            $zombieCount = 0;
            if ($zombieSessions->isNotEmpty()) {
                $this->warn("Found {$zombieSessions->count()} zombie FiveM session(s) (>24h active). Auto-closing...");

                foreach ($zombieSessions as $zombie) {
                    try {
                        DB::beginTransaction();

                        $clockInTime = $zombie->clock_in->copy()->setTimezone('Asia/Jakarta');
                        $clockOutTime = $now->copy();
                        
                        // Cap duration at 24 hours (since beyond that is likely a zombie)
                        $maxDurationSeconds = 24 * 3600;
                        $actualDuration = $clockInTime->diffInSeconds($clockOutTime);
                        $cappedDuration = min($actualDuration, $maxDurationSeconds);

                        $zombie->update([
                            'clock_out' => $clockOutTime,
                            'is_active' => false,
                            'session_duration' => $cappedDuration,
                            'total_hours' => max(1, floor($cappedDuration / 60)),
                            'notes' => trim(($zombie->notes ?? '') . "\n[Auto-closed: Zombie FiveM session >24h, capped at 24h]"),
                        ]);

                        // Reset user status
                        $user = \App\Models\User::find($zombie->user_id);
                        if ($user && !Attendance::where('user_id', $zombie->user_id)->where('is_active', true)->where('id', '!=', $zombie->id)->exists()) {
                            $user->update(['status' => 'offline']);
                        }

                        // Also close corresponding absensi record if exists
                        $absensi = \App\Models\Absensi::where('clock_in', $zombie->clock_in)
                            ->whereNull('clock_out')
                            ->first();
                        if ($absensi) {
                            $absensi->update([
                                'clock_out' => $clockOutTime,
                                'time_on_duty' => gmdate('H:i:s', $cappedDuration),
                                'source' => 'automatic_cleanup'
                            ]);
                        }

                        DB::commit();
                        $zombieCount++;

                        $this->info("Zombie session closed: ID {$zombie->id} (User: {$zombie->user_id}, Active since: {$clockInTime->toDateTimeString()})");

                        Log::info('Zombie FiveM session auto-closed', [
                            'attendance_id' => $zombie->id,
                            'user_id' => $zombie->user_id,
                            'clock_in' => $clockInTime->toDateTimeString(),
                            'duration_hours' => round($cappedDuration / 3600, 2),
                            'command' => 'attendance:check-expired-sessions'
                        ]);
                    } catch (\Exception $e) {
                        DB::rollBack();
                        $this->error("Failed to close zombie session ID: {$zombie->id} - {$e->getMessage()}");
                        Log::error('Failed to close zombie session', [
                            'attendance_id' => $zombie->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }

            // === ZOMBIE ABSENSI CLEANUP ===
            // Close absensi records that have been open for more than 48 hours (very old zombies)
            $absensiZombieThreshold = $now->copy()->subHours(48);
            $zombieAbsensi = \App\Models\Absensi::whereNull('clock_out')
                ->where('clock_in', '<', $absensiZombieThreshold)
                ->get();

            $absensiZombieCount = 0;
            if ($zombieAbsensi->isNotEmpty()) {
                $this->warn("Found {$zombieAbsensi->count()} zombie absensi record(s) (>48h). Auto-closing...");
                foreach ($zombieAbsensi as $za) {
                    try {
                        $cappedDuration = min($za->clock_in->diffInSeconds($now), 24 * 3600);
                        $za->update([
                            'clock_out' => $now,
                            'time_on_duty' => gmdate('H:i:s', $cappedDuration),
                            'source' => 'automatic_cleanup',
                            'notes' => 'Auto-closed: zombie absensi >48h'
                        ]);
                        $absensiZombieCount++;
                    } catch (\Exception $e) {
                        $this->error("Failed to close zombie absensi ID: {$za->id} - {$e->getMessage()}");
                    }
                }
            }

            $this->info("Processing complete. Scheduled: {$successCount} ok, {$failCount} failed. Zombies: {$zombieCount} attendance, {$absensiZombieCount} absensi closed.");

            if ($failCount > 0) {
                Log::alert('[AUTO-CHECKOUT] Command completed with failures', [
                    'success_count'       => $successCount,
                    'fail_count'          => $failCount,
                    'zombie_count'        => $zombieCount,
                    'absensi_zombie_count' => $absensiZombieCount,
                ]);
            }

            return 0;
        } catch (\Exception $e) {
            $this->error("Command failed: {$e->getMessage()}");
            Log::error('[AUTO-CHECKOUT] CheckExpiredDutySessions command CRASHED', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }

    /**
     * Kirim alert ke Discord jika ada sesi yang terlambat di-auto-close
     * (indikasi cron job tidak berjalan)
     *
     * @param array $overdueAlerts
     * @param \Carbon\Carbon $now
     */
    private function sendOverdueAlert(array $overdueAlerts, Carbon $now): void
    {
        try {
            $webhookUrl = config('services.discord.webhook_absensi', env('DISCORD_WEBHOOK_ABSENSI'));
            if (!$webhookUrl) {
                Log::warning('[AUTO-CHECKOUT] Discord webhook not configured — overdue alert not sent');
                return;
            }

            $count = count($overdueAlerts);
            $maxOverdue = max(array_column($overdueAlerts, 'overdue_minutes'));
            $userIds = implode(', ', array_unique(array_column($overdueAlerts, 'user_id')));

            $message = [
                'embeds' => [
                    [
                        'title'       => '⚠️ AUTO-CHECKOUT OVERDUE ALERT',
                        'description' => "**{$count} sesi** tidak di-auto-close tepat waktu!\n"
                            . "Terlambat maksimal **{$maxOverdue} menit**.\n"
                            . "Kemungkinan besar **cron job `schedule:run` tidak aktif** di server!\n"
                            . "User IDs terdampak: `{$userIds}`",
                        'color'       => 0xFF4444,
                        'timestamp'   => $now->toIso8601String(),
                        'footer'      => ['text' => 'EMS-IME Auto-Checkout Monitor'],
                        'fields'      => [
                            [
                                'name'   => '🕐 Waktu Deteksi',
                                'value'  => $now->format('d/m/Y H:i:s') . ' WIB',
                                'inline' => true,
                            ],
                            [
                                'name'   => '✅ Action',
                                'value'  => 'Sesi telah otomatis ditutup sekarang.',
                                'inline' => true,
                            ],
                            [
                                'name'   => '🔧 Tindakan Diperlukan',
                                'value'  => 'Periksa cron job di server:\n```* * * * * php artisan schedule:run```',
                                'inline' => false,
                            ],
                        ],
                    ]
                ]
            ];

            Http::timeout(5)->post($webhookUrl, $message);

            Log::warning('[AUTO-CHECKOUT] Overdue alert sent to Discord', [
                'overdue_count' => $count,
                'max_overdue_minutes' => $maxOverdue,
            ]);
        } catch (\Exception $e) {
            Log::warning('[AUTO-CHECKOUT] Failed to send overdue Discord alert', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
