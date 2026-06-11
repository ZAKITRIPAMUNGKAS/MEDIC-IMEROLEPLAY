<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\Absensi;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FixDataSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:fix-sync {--dry-run : Show what would be fixed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix data synchronization issues between attendances and absensi tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        $this->info('🔧 Memulai perbaikan sinkronisasi data...');
        
        if ($isDryRun) {
            $this->warn('⚠️  DRY RUN MODE - Tidak ada perubahan yang akan dibuat');
        }
        
        $totalFixed = 0;
        
        // 1. Fix inconsistent duration data in attendances table
        $this->info('📊 Memperbaiki data durasi yang tidak konsisten di tabel attendances...');
        $fixed = $this->fixAttendanceDurationData($isDryRun);
        $totalFixed += $fixed;
        $this->info("✅ Diperbaiki: {$fixed} record");
        
        // 2. Fix missing user mappings
        $this->info('👥 Memperbaiki mapping user yang hilang...');
        $fixed = $this->fixUserMappings($isDryRun);
        $totalFixed += $fixed;
        $this->info("✅ Diperbaiki: {$fixed} record");
        
        // 3. Fix cross-day sessions
        $this->info('📅 Memperbaiki sesi cross-day...');
        $fixed = $this->fixCrossDaySessions($isDryRun);
        $totalFixed += $fixed;
        $this->info("✅ Diperbaiki: {$fixed} record");
        
        // 4. Fix timezone inconsistencies
        $this->info('🌏 Memperbaiki inkonsistensi timezone...');
        $fixed = $this->fixTimezoneIssues($isDryRun);
        $totalFixed += $fixed;
        $this->info("✅ Diperbaiki: {$fixed} record");
        
        // 5. Generate sync report
        $this->generateSyncReport();
        
        $this->info("🎉 Selesai! Total {$totalFixed} masalah diperbaiki.");
        
        if (!$isDryRun) {
            $this->info('💡 Jalankan command ini secara berkala untuk menjaga sinkronisasi data.');
        }
    }
    
    /**
     * Fix inconsistent duration data in attendances table
     */
    private function fixAttendanceDurationData($isDryRun = false)
    {
        if ($isDryRun) {
            return Attendance::whereNotNull('clock_out')
                ->where(function($query) {
                    $query->whereNull('session_duration')
                          ->orWhereNull('total_hours')
                          ->orWhereRaw('session_duration != (total_hours * 60)');
                })
                ->count();
        }
        
        // Direct SQL update is extremely fast and avoids memory limit crashes!
        $affectedRows = DB::update("
            UPDATE attendances 
            SET 
                session_duration = TIMESTAMPDIFF(SECOND, clock_in, clock_out),
                total_hours = GREATEST(1, FLOOR(TIMESTAMPDIFF(SECOND, clock_in, clock_out) / 60))
            WHERE clock_out IS NOT NULL 
              AND (session_duration IS NULL OR total_hours IS NULL OR session_duration != (total_hours * 60))
        ");
        
        return $affectedRows;
    }
    
    /**
     * Fix missing user mappings
     */
    private function fixUserMappings($isDryRun = false)
    {
        $fixed = 0;
        
        // Check if absensi table exists
        if (!\Schema::hasTable('absensi')) {
            $this->warn('⚠️  Tabel absensi belum ada, melewati perbaikan mapping user...');
            return $fixed;
        }
        
        // Use chunking to avoid memory exhaustion
        Absensi::whereDoesntHave('user', function($query) {
            $query->whereColumn('users.staff_id', 'absensi.player_id');
        })->chunk(1000, function ($absensiRecords) use (&$fixed, $isDryRun) {
            foreach ($absensiRecords as $record) {
                // Try to find user by staff_id
                $user = User::where('staff_id', $record->player_id)->first();
                
                if (!$user) {
                    // Try to find by citizen_id
                    $user = User::where('citizen_id', $record->player_id)->first();
                }
                
                if (!$user) {
                    // Try to find by name similarity
                    $user = User::where('name', 'LIKE', '%' . $record->player_name . '%')->first();
                }
                
                if ($user && !$isDryRun) {
                    // Update user's staff_id if missing
                    if (!$user->staff_id) {
                        $user->update(['staff_id' => $record->player_id]);
                    }
                }
                
                $fixed++;
            }
        });
        
        return $fixed;
    }
    
    /**
     * Fix cross-day sessions
     */
    private function fixCrossDaySessions($isDryRun = false)
    {
        if (!$isDryRun) {
            return Attendance::fixCrossDaySessions();
        }
        
        // Count cross-day sessions for dry run
        return Attendance::whereNotNull('clock_out')
            ->whereRaw('DATE(clock_in) != DATE(clock_out)')
            ->count();
    }
    
    /**
     * Fix timezone inconsistencies
     */
    private function fixTimezoneIssues($isDryRun = false)
    {
        $fixed = 0;
        
        // Find records with potential timezone issues
        $records = Attendance::where('clock_in', '>', now()->addHours(12))
            ->orWhere('clock_out', '>', now()->addHours(12))
            ->get();
            
        foreach ($records as $record) {
            if (!$isDryRun) {
                // Convert to Asia/Jakarta timezone
                $record->update([
                    'clock_in' => $record->clock_in->setTimezone('Asia/Jakarta'),
                    'clock_out' => $record->clock_out ? $record->clock_out->setTimezone('Asia/Jakarta') : null
                ]);
            }
            
            $fixed++;
        }
        
        return $fixed;
    }
    
    /**
     * Generate synchronization report
     */
    private function generateSyncReport()
    {
        $this->info('📋 Laporan Sinkronisasi Data:');
        
        // Count records by type
        $manualCount = Attendance::count();
        $automaticCount = \Schema::hasTable('absensi') ? Absensi::count() : 0;
        $activeSessions = Attendance::where('is_active', true)->count();
        $activeAbsensi = \Schema::hasTable('absensi') ? Absensi::whereNull('clock_out')->count() : 0;
        
        $this->table(
            ['Tipe Data', 'Total Record', 'Sesi Aktif'],
            [
                ['Manual (attendances)', $manualCount, $activeSessions],
                ['Otomatis (absensi)', $automaticCount, $activeAbsensi]
            ]
        );
        
        // Check for inconsistencies
        $inconsistentDuration = Attendance::whereNotNull('clock_out')
            ->whereRaw('session_duration != (total_hours * 60)')
            ->count();
            
        $crossDaySessions = Attendance::whereNotNull('clock_out')
            ->whereRaw('DATE(clock_in) != DATE(clock_out)')
            ->count();
            
        if ($inconsistentDuration > 0 || $crossDaySessions > 0) {
            $this->warn('⚠️  Masalah yang ditemukan:');
            if ($inconsistentDuration > 0) {
                $this->warn("   - {$inconsistentDuration} record dengan durasi tidak konsisten");
            }
            if ($crossDaySessions > 0) {
                $this->warn("   - {$crossDaySessions} sesi cross-day");
            }
        } else {
            $this->info('✅ Tidak ada masalah sinkronisasi yang ditemukan!');
        }
    }
}
