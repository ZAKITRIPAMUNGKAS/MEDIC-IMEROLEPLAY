<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\AttendanceIntegrationService;
use App\Models\Absensi;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttendanceIntegrationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create([
            'staff_id' => '12345',
            'name' => 'Test User'
        ]);

        $this->service = new AttendanceIntegrationService();
    }

    /**
     * Test successful integration without conflicts
     */
    public function test_successful_integration_without_conflicts(): void
    {
        $result = $this->service->integrateAttendanceData(
            '12345',
            'Test User',
            now()->subHour()->toDateTimeString(),
            now()->toDateTimeString(),
            '01:00:00'
        );

        $this->assertTrue($result['success']);
        $this->assertEquals('Data absensi berhasil diintegrasikan', $result['message']);

        // Check that absensi record was created
        $this->assertDatabaseHas('absensi', [
            'player_id' => '12345',
            'player_name' => 'Test User',
            'source' => 'automatic'
        ]);

        // Check that manual attendance record was created
        $this->assertDatabaseHas('attendances', [
            'user_id' => $this->user->id,
            'session_type' => 'work',
            'is_active' => false
        ]);
    }

    /**
     * Test integration with user not found
     */
    public function test_integration_user_not_found(): void
    {
        $result = $this->service->integrateAttendanceData(
            '99999',
            'Non Existent User',
            now()->subHour()->toDateTimeString(),
            now()->toDateTimeString(),
            '01:00:00'
        );

        $this->assertFalse($result['success']);
        $this->assertStringContains('User tidak ditemukan', $result['message']);
    }

    /**
     * Test integration with active session conflict
     */
    public function test_integration_with_active_session_conflict(): void
    {
        // Create active manual session
        Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => now()->subHour(),
            'work_date' => today(),
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => true
        ]);

        $result = $this->service->integrateAttendanceData(
            '12345',
            'Test User',
            now()->subHour()->toDateTimeString(),
            now()->toDateTimeString(),
            '01:00:00'
        );

        $this->assertTrue($result['success']);
        $this->assertEquals('Data absensi manual diupdate dengan data otomatis', $result['message']);
        $this->assertEquals('manual', $result['priority']);

        // Check that manual session was updated
        $attendance = Attendance::where('user_id', $this->user->id)->first();
        $this->assertNotNull($attendance->clock_out);
        $this->assertFalse($attendance->is_active);
    }

    /**
     * Test integration with overlapping session conflict
     */
    public function test_integration_with_overlapping_session_conflict(): void
    {
        // Create overlapping manual session
        Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => now()->subMinutes(30),
            'clock_out' => now()->addMinutes(30),
            'work_date' => today(),
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => false,
            'session_duration' => 3600
        ]);

        $result = $this->service->integrateAttendanceData(
            '12345',
            'Test User',
            now()->subHour()->toDateTimeString(),
            now()->toDateTimeString(),
            '01:00:00'
        );

        $this->assertTrue($result['success']);
        $this->assertEquals('Data absensi otomatis disimpan dengan catatan konflik', $result['message']);
        $this->assertEquals('manual', $result['priority']);

        // Check that absensi record was created with conflict note
        $absensi = Absensi::where('player_id', '12345')->first();
        $this->assertStringContains('Konflik dengan sesi manual', $absensi->notes);
    }

    /**
     * Test find user by player ID
     */
    public function test_find_user_by_player_id(): void
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('findUserByPlayerId');
        $method->setAccessible(true);

        // Test finding by staff_id
        $user = $method->invoke($this->service, '12345', 'Test User');
        $this->assertNotNull($user);
        $this->assertEquals($this->user->id, $user->id);

        // Test finding by name
        $userByName = $method->invoke($this->service, '99999', 'Test User');
        $this->assertNotNull($userByName);
        $this->assertEquals($this->user->id, $userByName->id);

        // Test user not found
        $notFound = $method->invoke($this->service, '99999', 'Non Existent');
        $this->assertNull($notFound);
    }

    /**
     * Test check manual attendance conflict
     */
    public function test_check_manual_attendance_conflict(): void
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('checkManualAttendanceConflict');
        $method->setAccessible(true);

        // Test no conflict
        $noConflict = $method->invoke(
            $this->service,
            $this->user->id,
            now()->subHour()->toDateTimeString(),
            now()->toDateTimeString()
        );
        $this->assertFalse($noConflict['has_conflict']);

        // Test active session conflict
        Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => now()->subHour(),
            'work_date' => today(),
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => true
        ]);

        $activeConflict = $method->invoke(
            $this->service,
            $this->user->id,
            now()->subHour()->toDateTimeString(),
            now()->toDateTimeString()
        );
        $this->assertTrue($activeConflict['has_conflict']);
        $this->assertEquals('active_session', $activeConflict['type']);

        // Test overlapping session conflict
        Attendance::where('user_id', $this->user->id)->delete();
        Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => now()->subMinutes(30),
            'clock_out' => now()->addMinutes(30),
            'work_date' => today(),
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => false,
            'session_duration' => 3600
        ]);

        $overlapConflict = $method->invoke(
            $this->service,
            $this->user->id,
            now()->subHour()->toDateTimeString(),
            now()->toDateTimeString()
        );
        $this->assertTrue($overlapConflict['has_conflict']);
        $this->assertEquals('overlapping_session', $overlapConflict['type']);
    }

    /**
     * Test save automatic attendance
     */
    public function test_save_automatic_attendance(): void
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('saveAutomaticAttendance');
        $method->setAccessible(true);

        $absensi = $method->invoke(
            $this->service,
            '12345',
            'Test User',
            now()->subHour()->toDateTimeString(),
            now()->toDateTimeString(),
            '01:00:00'
        );

        $this->assertInstanceOf(Absensi::class, $absensi);
        $this->assertEquals('12345', $absensi->player_id);
        $this->assertEquals('Test User', $absensi->player_name);
        $this->assertEquals('automatic', $absensi->source);
    }

    /**
     * Test create manual attendance record
     */
    public function test_create_manual_attendance_record(): void
    {
        $absensi = Absensi::create([
            'player_id' => '12345',
            'player_name' => 'Test User',
            'clock_in' => now()->subHour(),
            'clock_out' => now(),
            'time_on_duty' => '01:00:00',
            'source' => 'automatic'
        ]);

        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('createManualAttendanceRecord');
        $method->setAccessible(true);

        $attendance = $method->invoke($this->service, $this->user, $absensi);

        $this->assertInstanceOf(Attendance::class, $attendance);
        $this->assertEquals($this->user->id, $attendance->user_id);
        $this->assertEquals('work', $attendance->session_type);
        $this->assertFalse($attendance->is_active);
        $this->assertStringContains('Generated from automatic attendance', $attendance->notes);
    }

    /**
     * Test calculate duration
     */
    public function test_calculate_duration(): void
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('calculateDuration');
        $method->setAccessible(true);

        $clockIn = now()->subHour();
        $clockOut = now();

        $duration = $method->invoke($this->service, $clockIn->toDateTimeString(), $clockOut->toDateTimeString());

        $this->assertEquals(60, $duration); // 1 hour in minutes
    }

    /**
     * Test get combined attendance data
     */
    public function test_get_combined_attendance_data(): void
    {
        // Create manual attendance
        Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => now()->subHour(),
            'clock_out' => now(),
            'work_date' => today(),
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => false,
            'session_duration' => 3600
        ]);

        // Create automatic attendance
        Absensi::create([
            'player_id' => '12345',
            'player_name' => 'Test User',
            'clock_in' => now()->subHours(2),
            'clock_out' => now()->subHour(),
            'time_on_duty' => '01:00:00',
            'source' => 'automatic'
        ]);

        $data = $this->service->getCombinedAttendanceData($this->user->id);

        $this->assertArrayHasKey('manual', $data);
        $this->assertArrayHasKey('automatic', $data);
        $this->assertArrayHasKey('combined', $data);

        $this->assertEquals(1, $data['manual']->count());
        $this->assertEquals(1, $data['automatic']->count());
        $this->assertEquals(2, $data['combined']->count());
    }

    /**
     * Test get total work hours
     */
    public function test_get_total_work_hours(): void
    {
        // Create manual attendance
        Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => now()->subHour(),
            'clock_out' => now(),
            'work_date' => today(),
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => false,
            'session_duration' => 3600 // 1 hour in seconds
        ]);

        // Create automatic attendance
        Absensi::create([
            'player_id' => '12345',
            'player_name' => 'Test User',
            'clock_in' => now()->subHours(2),
            'clock_out' => now()->subHour(),
            'time_on_duty' => '01:00:00',
            'source' => 'automatic'
        ]);

        $totalHours = $this->service->getTotalWorkHours($this->user->id, 'month');

        $this->assertArrayHasKey('total_minutes', $totalHours);
        $this->assertArrayHasKey('total_hours', $totalHours);
        $this->assertArrayHasKey('formatted_time', $totalHours);

        $this->assertGreaterThan(0, $totalHours['total_minutes']);
        $this->assertGreaterThan(0, $totalHours['total_hours']);
    }

    /**
     * Test period start and end dates
     */
    public function test_period_dates(): void
    {
        $reflection = new \ReflectionClass($this->service);

        // Test week period
        $getPeriodStart = $reflection->getMethod('getPeriodStart');
        $getPeriodStart->setAccessible(true);
        $weekStart = $getPeriodStart->invoke($this->service, 'week');
        $this->assertEquals(now()->startOfWeek()->toDateString(), $weekStart->toDateString());

        // Test month period
        $monthStart = $getPeriodStart->invoke($this->service, 'month');
        $this->assertEquals(now()->startOfMonth()->toDateString(), $monthStart->toDateString());

        // Test year period
        $yearStart = $getPeriodStart->invoke($this->service, 'year');
        $this->assertEquals(now()->startOfYear()->toDateString(), $yearStart->toDateString());

        // Test period end
        $getPeriodEnd = $reflection->getMethod('getPeriodEnd');
        $getPeriodEnd->setAccessible(true);
        $weekEnd = $getPeriodEnd->invoke($this->service, 'week');
        $this->assertEquals(now()->endOfWeek()->toDateString(), $weekEnd->toDateString());
    }

    /**
     * Test format time
     */
    public function test_format_time(): void
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('formatTime');
        $method->setAccessible(true);

        // Test 1 hour 30 minutes
        $formatted = $method->invoke($this->service, 90);
        $this->assertEquals('01:30:00', $formatted);

        // Test 2 hours 45 minutes 30 seconds
        $formatted2 = $method->invoke($this->service, 165.5);
        $this->assertEquals('02:45:30', $formatted2);

        // Test zero time
        $formatted3 = $method->invoke($this->service, 0);
        $this->assertEquals('00:00:00', $formatted3);
    }

    /**
     * Test merge attendance data
     */
    public function test_merge_attendance_data(): void
    {
        // Create manual attendance
        $manual = collect([
            (object) [
                'id' => 1,
                'clock_in' => now()->subHour(),
                'clock_out' => now(),
                'session_duration' => 3600,
                'notes' => 'Manual session'
            ]
        ]);

        // Create automatic attendance
        $automatic = collect([
            (object) [
                'id' => 1,
                'clock_in' => now()->subHours(2),
                'clock_out' => now()->subHour(),
                'time_on_duty' => '01:00:00',
                'notes' => 'Automatic session'
            ]
        ]);

        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('mergeAttendanceData');
        $method->setAccessible(true);

        $merged = $method->invoke($this->service, $manual, $automatic);

        $this->assertEquals(2, $merged->count());
        $this->assertEquals('manual', $merged->first()['type']);
        $this->assertEquals('automatic', $merged->last()['type']);
    }

    /**
     * Test error handling in integration
     */
    public function test_error_handling_in_integration(): void
    {
        // Test with invalid data that should cause an exception
        $result = $this->service->integrateAttendanceData(
            '12345',
            'Test User',
            'invalid-date',
            'invalid-date',
            'invalid-time'
        );

        $this->assertFalse($result['success']);
        $this->assertStringContains('Terjadi kesalahan', $result['message']);
    }

    /**
     * Test integration with clock in only (no clock out)
     */
    public function test_integration_clock_in_only(): void
    {
        $result = $this->service->integrateAttendanceData(
            '12345',
            'Test User',
            now()->toDateTimeString(),
            null,
            null
        );

        $this->assertTrue($result['success']);

        // Check that absensi record was created without clock_out
        $this->assertDatabaseHas('absensi', [
            'player_id' => '12345',
            'clock_out' => null
        ]);

        // Check that manual attendance record was created as active
        $this->assertDatabaseHas('attendances', [
            'user_id' => $this->user->id,
            'is_active' => true
        ]);
    }
}
