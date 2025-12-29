<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Absensi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AbsensiModelTest extends TestCase
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
     * Test active scope
     */
    public function test_active_scope(): void
    {
        // Create active absensi (no clock_out)
        Absensi::create([
            'player_id' => '12345',
            'player_name' => 'Test User',
            'clock_in' => now(),
            'clock_out' => null
        ]);

        // Create completed absensi
        Absensi::create([
            'player_id' => '67890',
            'player_name' => 'Test User 2',
            'clock_in' => now()->subHour(),
            'clock_out' => now(),
            'time_on_duty' => '01:00:00'
        ]);

        $activeAbsensi = Absensi::active()->get();
        $this->assertEquals(1, $activeAbsensi->count());
        $this->assertNull($activeAbsensi->first()->clock_out);
    }

    /**
     * Test by player scope
     */
    public function test_by_player_scope(): void
    {
        // Create absensi for different players
        Absensi::create([
            'player_id' => '12345',
            'player_name' => 'Test User 1',
            'clock_in' => now()
        ]);

        Absensi::create([
            'player_id' => '67890',
            'player_name' => 'Test User 2',
            'clock_in' => now()
        ]);

        $playerAbsensi = Absensi::byPlayer('12345')->get();
        $this->assertEquals(1, $playerAbsensi->count());
        $this->assertEquals('12345', $playerAbsensi->first()->player_id);
    }

    /**
     * Test today scope
     */
    public function test_today_scope(): void
    {
        $today = today();

        // Create absensi for today
        Absensi::create([
            'player_id' => '12345',
            'player_name' => 'Test User',
            'clock_in' => $today->setTime(9, 0)
        ]);

        // Create absensi for yesterday
        Absensi::create([
            'player_id' => '67890',
            'player_name' => 'Test User 2',
            'clock_in' => $today->copy()->subDay()->setTime(9, 0)
        ]);

        $todayAbsensi = Absensi::today()->get();
        $this->assertEquals(1, $todayAbsensi->count());
        $this->assertEquals($today->toDateString(), $todayAbsensi->first()->clock_in->toDateString());
    }

    /**
     * Test this week scope
     */
    public function test_this_week_scope(): void
    {
        $startOfWeek = now()->startOfWeek();

        // Create absensi for this week
        Absensi::create([
            'player_id' => '12345',
            'player_name' => 'Test User',
            'clock_in' => $startOfWeek->addDays(2)
        ]);

        // Create absensi for last week
        Absensi::create([
            'player_id' => '67890',
            'player_name' => 'Test User 2',
            'clock_in' => $startOfWeek->copy()->subWeek()
        ]);

        $thisWeekAbsensi = Absensi::thisWeek()->get();
        $this->assertEquals(1, $thisWeekAbsensi->count());
    }

    /**
     * Test this month scope
     */
    public function test_this_month_scope(): void
    {
        $startOfMonth = now()->startOfMonth();

        // Create absensi for this month
        Absensi::create([
            'player_id' => '12345',
            'player_name' => 'Test User',
            'clock_in' => $startOfMonth->addDays(5)
        ]);

        // Create absensi for last month
        Absensi::create([
            'player_id' => '67890',
            'player_name' => 'Test User 2',
            'clock_in' => $startOfMonth->copy()->subMonth()
        ]);

        $thisMonthAbsensi = Absensi::thisMonth()->get();
        $this->assertEquals(1, $thisMonthAbsensi->count());
    }

    /**
     * Test this year scope
     */
    public function test_this_year_scope(): void
    {
        $startOfYear = now()->startOfYear();

        // Create absensi for this year
        Absensi::create([
            'player_id' => '12345',
            'player_name' => 'Test User',
            'clock_in' => $startOfYear->addMonths(3)
        ]);

        // Create absensi for last year
        Absensi::create([
            'player_id' => '67890',
            'player_name' => 'Test User 2',
            'clock_in' => $startOfYear->copy()->subYear()
        ]);

        $thisYearAbsensi = Absensi::thisYear()->get();
        $this->assertEquals(1, $thisYearAbsensi->count());
    }

    /**
     * Test completed scope
     */
    public function test_completed_scope(): void
    {
        // Create completed absensi
        Absensi::create([
            'player_id' => '12345',
            'player_name' => 'Test User',
            'clock_in' => now()->subHour(),
            'clock_out' => now(),
            'time_on_duty' => '01:00:00'
        ]);

        // Create active absensi
        Absensi::create([
            'player_id' => '67890',
            'player_name' => 'Test User 2',
            'clock_in' => now(),
            'clock_out' => null
        ]);

        $completedAbsensi = Absensi::completed()->get();
        $this->assertEquals(1, $completedAbsensi->count());
        $this->assertNotNull($completedAbsensi->first()->clock_out);
    }

    /**
     * Test date range scope
     */
    public function test_date_range_scope(): void
    {
        $startDate = now()->subDays(5);
        $endDate = now()->subDays(2);

        // Create absensi within range
        Absensi::create([
            'player_id' => '12345',
            'player_name' => 'Test User',
            'clock_in' => $startDate->addDays(1)
        ]);

        // Create absensi outside range
        Absensi::create([
            'player_id' => '67890',
            'player_name' => 'Test User 2',
            'clock_in' => now()
        ]);

        $rangeAbsensi = Absensi::dateRange($startDate, $endDate)->get();
        $this->assertEquals(1, $rangeAbsensi->count());
    }

    /**
     * Test is player active
     */
    public function test_is_player_active(): void
    {
        // Create active absensi
        Absensi::create([
            'player_id' => '12345',
            'player_name' => 'Test User',
            'clock_in' => now(),
            'clock_out' => null
        ]);

        $isActive = Absensi::isPlayerActive('12345');
        $this->assertTrue($isActive);

        $isNotActive = Absensi::isPlayerActive('67890');
        $this->assertFalse($isNotActive);
    }

    /**
     * Test get duration in seconds
     */
    public function test_get_duration_in_seconds(): void
    {
        $clockIn = now()->subHour();
        $clockOut = now();

        $absensi = Absensi::create([
            'player_id' => '12345',
            'player_name' => 'Test User',
            'clock_in' => $clockIn,
            'clock_out' => $clockOut,
            'time_on_duty' => '01:00:00'
        ]);

        $duration = $absensi->getDurationInSeconds();
        $this->assertNotNull($duration);
        $this->assertGreaterThan(0, $duration);

        // Test with no clock_out
        $activeAbsensi = Absensi::create([
            'player_id' => '67890',
            'player_name' => 'Test User 2',
            'clock_in' => now(),
            'clock_out' => null
        ]);

        $activeDuration = $activeAbsensi->getDurationInSeconds();
        $this->assertNull($activeDuration);
    }

    /**
     * Test get formatted duration
     */
    public function test_get_formatted_duration(): void
    {
        $absensi = Absensi::create([
            'player_id' => '12345',
            'player_name' => 'Test User',
            'clock_in' => now()->subHours(2)->subMinutes(30)->subSeconds(45),
            'clock_out' => now(),
            'time_on_duty' => '02:30:45'
        ]);

        $formattedDuration = $absensi->getFormattedDuration();
        $this->assertEquals('02:30:45', $formattedDuration);

        // Test with no clock_out
        $activeAbsensi = Absensi::create([
            'player_id' => '67890',
            'player_name' => 'Test User 2',
            'clock_in' => now(),
            'clock_out' => null
        ]);

        $activeFormattedDuration = $activeAbsensi->getFormattedDuration();
        $this->assertEquals('00:00:00', $activeFormattedDuration);
    }

    /**
     * Test get duration in hours
     */
    public function test_get_duration_in_hours(): void
    {
        $absensi = Absensi::create([
            'player_id' => '12345',
            'player_name' => 'Test User',
            'clock_in' => now()->subHour(),
            'clock_out' => now(),
            'time_on_duty' => '01:00:00'
        ]);

        $durationInHours = $absensi->getDurationInHours();
        $this->assertEquals(1.0, $durationInHours);

        // Test with no clock_out
        $activeAbsensi = Absensi::create([
            'player_id' => '67890',
            'player_name' => 'Test User 2',
            'clock_in' => now(),
            'clock_out' => null
        ]);

        $activeDurationInHours = $activeAbsensi->getDurationInHours();
        $this->assertEquals(0, $activeDurationInHours);
    }

    /**
     * Test user relationship
     */
    public function test_user_relationship(): void
    {
        $absensi = Absensi::create([
            'player_id' => '12345',
            'player_name' => 'Test User',
            'clock_in' => now()
        ]);

        $user = $absensi->user;
        $this->assertNotNull($user);
        $this->assertEquals($this->user->id, $user->id);
    }

    /**
     * Test fillable attributes
     */
    public function test_fillable_attributes(): void
    {
        $data = [
            'player_id' => '12345',
            'player_name' => 'Test User',
            'clock_in' => now(),
            'clock_out' => now()->addHour(),
            'time_on_duty' => '01:00:00',
            'source' => 'automatic',
            'notes' => 'Test notes'
        ];

        $absensi = Absensi::create($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $absensi->$key);
        }
    }

    /**
     * Test casts
     */
    public function test_casts(): void
    {
        $clockIn = now();
        $clockOut = now()->addHour();
        $timeOnDuty = '01:00:00';

        $absensi = Absensi::create([
            'player_id' => '12345',
            'player_name' => 'Test User',
            'clock_in' => $clockIn,
            'clock_out' => $clockOut,
            'time_on_duty' => $timeOnDuty
        ]);

        $this->assertInstanceOf(Carbon::class, $absensi->clock_in);
        $this->assertInstanceOf(Carbon::class, $absensi->clock_out);
        $this->assertIsString($absensi->time_on_duty);
    }

    /**
     * Test table name
     */
    public function test_table_name(): void
    {
        $absensi = new Absensi();
        $this->assertEquals('absensi', $absensi->getTable());
    }

    /**
     * Test mass assignment protection
     */
    public function test_mass_assignment_protection(): void
    {
        $data = [
            'player_id' => '12345',
            'player_name' => 'Test User',
            'clock_in' => now(),
            'clock_out' => now()->addHour(),
            'time_on_duty' => '01:00:00',
            'source' => 'automatic',
            'notes' => 'Test notes',
            'created_at' => now(), // This should be protected
            'updated_at' => now()  // This should be protected
        ];

        $absensi = Absensi::create($data);

        // Check that protected attributes are not set
        $this->assertNull($absensi->created_at);
        $this->assertNull($absensi->updated_at);
    }

    /**
     * Test edge cases for duration calculation
     */
    public function test_duration_calculation_edge_cases(): void
    {
        // Test very short duration
        $absensi = Absensi::create([
            'player_id' => '12345',
            'player_name' => 'Test User',
            'clock_in' => now()->subSecond(),
            'clock_out' => now(),
            'time_on_duty' => '00:00:01'
        ]);

        $duration = $absensi->getDurationInSeconds();
        $this->assertGreaterThan(0, $duration);

        // Test same time (edge case)
        $sameTime = now();
        $absensiSameTime = Absensi::create([
            'player_id' => '67890',
            'player_name' => 'Test User 2',
            'clock_in' => $sameTime,
            'clock_out' => $sameTime,
            'time_on_duty' => '00:00:00'
        ]);

        $sameTimeDuration = $absensiSameTime->getDurationInSeconds();
        $this->assertEquals(0, $sameTimeDuration);
    }

    /**
     * Test time on duty format validation
     */
    public function test_time_on_duty_format(): void
    {
        $validFormats = [
            '01:00:00',
            '12:30:45',
            '00:00:01',
            '23:59:59'
        ];

        foreach ($validFormats as $format) {
            $absensi = Absensi::create([
                'player_id' => '12345',
                'player_name' => 'Test User',
                'clock_in' => now(),
                'clock_out' => now()->addHour(),
                'time_on_duty' => $format
            ]);

            $this->assertEquals($format, $absensi->time_on_duty);
        }
    }

    /**
     * Test player name sanitization
     */
    public function test_player_name_sanitization(): void
    {
        $specialCharacters = 'Test User <script>alert("xss")</script>';
        
        $absensi = Absensi::create([
            'player_id' => '12345',
            'player_name' => $specialCharacters,
            'clock_in' => now()
        ]);

        // The model should store the name as-is, sanitization should happen in controller
        $this->assertEquals($specialCharacters, $absensi->player_name);
    }

    /**
     * Test player ID format validation
     */
    public function test_player_id_format(): void
    {
        $validPlayerIds = [
            '12345',
            'player_123',
            'user:123',
            'test_user_123'
        ];

        foreach ($validPlayerIds as $playerId) {
            $absensi = Absensi::create([
                'player_id' => $playerId,
                'player_name' => 'Test User',
                'clock_in' => now()
            ]);

            $this->assertEquals($playerId, $absensi->player_id);
        }
    }
}
