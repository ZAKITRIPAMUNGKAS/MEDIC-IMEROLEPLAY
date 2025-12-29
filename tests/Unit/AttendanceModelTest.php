<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttendanceModelTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create([
            'staff_id' => '12345',
            'name' => 'Test User'
        ]);
    }

    /**
     * Test active session scope
     */
    public function test_active_scope(): void
    {
        // Create active session
        Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => now(),
            'work_date' => today(),
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => true
        ]);

        // Create inactive session
        Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => now()->subHour(),
            'clock_out' => now(),
            'work_date' => today(),
            'session_number' => 2,
            'session_type' => 'work',
            'is_active' => false
        ]);

        $activeSessions = Attendance::active()->get();
        $this->assertEquals(1, $activeSessions->count());
        $this->assertTrue($activeSessions->first()->is_active);
    }

    /**
     * Test for date scope
     */
    public function test_for_date_scope(): void
    {
        $today = today();
        $yesterday = today()->subDay();

        // Create sessions for different dates
        Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => $today->setTime(9, 0),
            'work_date' => $today,
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => true
        ]);

        Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => $yesterday->setTime(9, 0),
            'work_date' => $yesterday,
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => true
        ]);

        $todaySessions = Attendance::forDate($today)->get();
        $this->assertEquals(1, $todaySessions->count());
        $this->assertEquals($today->toDateString(), $todaySessions->first()->work_date->toDateString());
    }

    /**
     * Test for user scope
     */
    public function test_for_user_scope(): void
    {
        $anotherUser = User::factory()->create();

        // Create sessions for different users
        Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => now(),
            'work_date' => today(),
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => true
        ]);

        Attendance::create([
            'user_id' => $anotherUser->id,
            'clock_in' => now(),
            'work_date' => today(),
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => true
        ]);

        $userSessions = Attendance::forUser($this->user->id)->get();
        $this->assertEquals(1, $userSessions->count());
        $this->assertEquals($this->user->id, $userSessions->first()->user_id);
    }

    /**
     * Test completed scope
     */
    public function test_completed_scope(): void
    {
        // Create completed session
        Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => now()->subHour(),
            'clock_out' => now(),
            'work_date' => today(),
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => false
        ]);

        // Create active session
        Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => now(),
            'work_date' => today(),
            'session_number' => 2,
            'session_type' => 'work',
            'is_active' => true
        ]);

        $completedSessions = Attendance::completed()->get();
        $this->assertEquals(1, $completedSessions->count());
        $this->assertNotNull($completedSessions->first()->clock_out);
        $this->assertFalse($completedSessions->first()->is_active);
    }

    /**
     * Test valid scope
     */
    public function test_valid_scope(): void
    {
        // Create valid session
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

        // Create invalid session (no duration)
        Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => now(),
            'work_date' => today(),
            'session_number' => 2,
            'session_type' => 'work',
            'is_active' => true,
            'session_duration' => 0
        ]);

        $validSessions = Attendance::valid()->get();
        $this->assertEquals(1, $validSessions->count());
        $this->assertGreaterThan(0, $validSessions->first()->session_duration);
    }

    /**
     * Test get active session
     */
    public function test_get_active_session(): void
    {
        $date = today();

        // Create active session
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => now(),
            'work_date' => $date,
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => true
        ]);

        $activeSession = Attendance::getActiveSession($this->user->id, $date);
        $this->assertNotNull($activeSession);
        $this->assertEquals($attendance->id, $activeSession->id);
    }

    /**
     * Test get any active session
     */
    public function test_get_any_active_session(): void
    {
        // Create active session from yesterday
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => yesterday(),
            'work_date' => yesterday(),
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => true
        ]);

        $activeSession = Attendance::getAnyActiveSession($this->user->id);
        $this->assertNotNull($activeSession);
        $this->assertEquals($attendance->id, $activeSession->id);
    }

    /**
     * Test get daily sessions
     */
    public function test_get_daily_sessions(): void
    {
        $date = today();

        // Create multiple sessions for the same day
        Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => $date->setTime(9, 0),
            'clock_out' => $date->setTime(12, 0),
            'work_date' => $date,
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => false,
            'session_duration' => 10800
        ]);

        Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => $date->setTime(13, 0),
            'work_date' => $date,
            'session_number' => 2,
            'session_type' => 'work',
            'is_active' => true
        ]);

        $dailySessions = Attendance::getDailySessions($this->user->id, $date);
        $this->assertEquals(2, $dailySessions->count());
        $this->assertEquals(1, $dailySessions->first()->session_number);
        $this->assertEquals(2, $dailySessions->last()->session_number);
    }

    /**
     * Test get next session number
     */
    public function test_get_next_session_number(): void
    {
        $date = today();

        // Create first session
        Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => now(),
            'work_date' => $date,
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => true
        ]);

        $nextSessionNumber = Attendance::getNextSessionNumber($this->user->id, $date);
        $this->assertEquals(2, $nextSessionNumber);

        // Test for new user (should start from 1)
        $newUser = User::factory()->create();
        $firstSessionNumber = Attendance::getNextSessionNumber($newUser->id, $date);
        $this->assertEquals(1, $firstSessionNumber);
    }

    /**
     * Test get daily total hours
     */
    public function test_get_daily_total_hours(): void
    {
        $date = today();

        // Create sessions with different durations
        Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => $date->setTime(9, 0),
            'clock_out' => $date->setTime(12, 0),
            'work_date' => $date,
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => false,
            'session_duration' => 10800 // 3 hours
        ]);

        Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => $date->setTime(13, 0),
            'clock_out' => $date->setTime(17, 0),
            'work_date' => $date,
            'session_number' => 2,
            'session_type' => 'work',
            'is_active' => false,
            'session_duration' => 14400 // 4 hours
        ]);

        $totalHours = Attendance::getDailyTotalHours($this->user->id, $date);
        $this->assertEquals(25200, $totalHours); // 7 hours in seconds
    }

    /**
     * Test close session functionality
     */
    public function test_close_session(): void
    {
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => now()->subHour(),
            'work_date' => today(),
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => true
        ]);

        $result = $attendance->closeSession();
        $this->assertTrue($result);

        $attendance->refresh();
        $this->assertNotNull($attendance->clock_out);
        $this->assertFalse($attendance->is_active);
        $this->assertGreaterThan(0, $attendance->session_duration);
    }

    /**
     * Test close session with invalid data
     */
    public function test_close_session_invalid(): void
    {
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => now()->addHour(), // Future time
            'work_date' => today(),
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => true
        ]);

        $result = $attendance->closeSession();
        $this->assertFalse($result);
    }

    /**
     * Test calculate total hours
     */
    public function test_calculate_total_hours(): void
    {
        // Test with session_duration
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => now()->subHour(),
            'clock_out' => now(),
            'work_date' => today(),
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => false,
            'session_duration' => 3600
        ]);

        $totalHours = $attendance->calculateTotalHours();
        $this->assertEquals(3600, $totalHours);

        // Test with active session
        $activeAttendance = Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => now()->subMinutes(30),
            'work_date' => today(),
            'session_number' => 2,
            'session_type' => 'work',
            'is_active' => true
        ]);

        $activeTotalHours = $activeAttendance->calculateTotalHours();
        $this->assertGreaterThan(0, $activeTotalHours);
    }

    /**
     * Test get formatted duration
     */
    public function test_get_formatted_duration(): void
    {
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => now()->subHours(2)->subMinutes(30)->subSeconds(45),
            'clock_out' => now(),
            'work_date' => today(),
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => false,
            'session_duration' => 9045 // 2 hours, 30 minutes, 45 seconds
        ]);

        $formattedDuration = $attendance->getFormattedDuration();
        $this->assertEquals('02:30:45', $formattedDuration);
    }

    /**
     * Test get duration in hours
     */
    public function test_get_duration_in_hours(): void
    {
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => now()->subHour(),
            'clock_out' => now(),
            'work_date' => today(),
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => false,
            'session_duration' => 3600
        ]);

        $durationInHours = $attendance->getDurationInHours();
        $this->assertEquals(1.0, $durationInHours);
    }

    /**
     * Test cross day detection
     */
    public function test_is_cross_day(): void
    {
        // Create cross-day session
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => yesterday()->setTime(23, 30),
            'clock_out' => today()->setTime(1, 30),
            'work_date' => yesterday(),
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => false,
            'session_duration' => 7200
        ]);

        $this->assertTrue($attendance->isCrossDay());

        // Create same-day session
        $sameDayAttendance = Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => today()->setTime(9, 0),
            'clock_out' => today()->setTime(17, 0),
            'work_date' => today(),
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => false,
            'session_duration' => 28800
        ]);

        $this->assertFalse($sameDayAttendance->isCrossDay());
    }

    /**
     * Test cross week detection
     */
    public function test_is_cross_week(): void
    {
        // Create cross-week session
        $lastWeek = now()->subWeek();
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => $lastWeek->endOfWeek()->subHour(),
            'clock_out' => $lastWeek->addWeek()->startOfWeek()->addHour(),
            'work_date' => $lastWeek->toDateString(),
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => false,
            'session_duration' => 7200
        ]);

        $this->assertTrue($attendance->isCrossWeek());
    }

    /**
     * Test fix inconsistent data
     */
    public function test_fix_inconsistent_data(): void
    {
        // Create record with null session_duration but clock_out exists
        Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => now()->subHour(),
            'clock_out' => now(),
            'work_date' => today(),
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => false,
            'session_duration' => null
        ]);

        $fixed = Attendance::fixInconsistentData();
        $this->assertEquals(1, $fixed);

        $attendance = Attendance::first();
        $this->assertNotNull($attendance->session_duration);
        $this->assertGreaterThan(0, $attendance->session_duration);
    }

    /**
     * Test cross-day session splitting
     */
    public function test_split_cross_day_session(): void
    {
        $yesterday = yesterday();
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => $yesterday->setTime(23, 30),
            'clock_out' => today()->setTime(1, 30),
            'work_date' => $yesterday,
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => false,
            'session_duration' => 7200
        ]);

        $result = $attendance->splitCrossDaySession();
        $this->assertTrue($result);

        // Should have 2 records now
        $sessions = Attendance::where('user_id', $this->user->id)
            ->orderBy('work_date')
            ->get();

        $this->assertEquals(2, $sessions->count());
        $this->assertEquals($yesterday->toDateString(), $sessions->first()->work_date->toDateString());
        $this->assertEquals(today()->toDateString(), $sessions->last()->work_date->toDateString());
    }

    /**
     * Test fix cross-day sessions
     */
    public function test_fix_cross_day_sessions(): void
    {
        // Create cross-day session
        $yesterday = yesterday();
        Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => $yesterday->setTime(23, 30),
            'clock_out' => today()->setTime(1, 30),
            'work_date' => $yesterday,
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => false,
            'session_duration' => 7200
        ]);

        $fixed = Attendance::fixCrossDaySessions();
        $this->assertEquals(1, $fixed);

        // Should have 2 records now
        $sessions = Attendance::where('user_id', $this->user->id)->count();
        $this->assertEquals(2, $sessions);
    }

    /**
     * Test get today sessions
     */
    public function test_get_today_sessions(): void
    {
        $today = today();

        // Create session for today
        Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => $today->setTime(9, 0),
            'work_date' => $today,
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => true
        ]);

        // Create session for yesterday
        Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => yesterday()->setTime(9, 0),
            'work_date' => yesterday(),
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => true
        ]);

        $todaySessions = Attendance::getTodaySessions($this->user->id);
        $this->assertEquals(1, $todaySessions->count());
        $this->assertEquals($today->toDateString(), $todaySessions->first()->work_date->toDateString());
    }

    /**
     * Test get today active session
     */
    public function test_get_today_active_session(): void
    {
        $today = today();

        // Create active session for today
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => $today->setTime(9, 0),
            'work_date' => $today,
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => true
        ]);

        $activeSession = Attendance::getTodayActiveSession($this->user->id);
        $this->assertNotNull($activeSession);
        $this->assertEquals($attendance->id, $activeSession->id);
    }
}
