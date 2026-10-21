<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Absensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ClockInOutTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        \Illuminate\Support\Facades\Cache::flush();
        config(['app.api_key' => 'test-api-key-123']);

        $role = \App\Models\StaffRole::create([
            'name' => 'paramedic',
            'display_name' => 'Paramedic',
            'permissions' => []
        ]);

        // Create test user
        $this->user = User::factory()->create([
            'staff_id' => '12345',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role_id' => $role->id,
            'is_active' => true,
            'status' => 'offline'
        ]);
    }

    /**
     * Test manual clock in functionality
     */
    public function test_manual_clock_in_success(): void
    {
        $this->actingAs($this->user);

        $response = $this->post('/staff/attendance/clock-in', [
            'session_type' => 'work',
            'notes' => 'Test clock in'
        ]);

        $response->assertRedirect(route('staff.meeting-requests.create'))
                ->assertSessionHas('info');
    }

    /**
     * Test manual clock in with duplicate active session
     */
    public function test_manual_clock_in_duplicate_session(): void
    {
        $this->actingAs($this->user);

        $response = $this->post('/staff/attendance/clock-in', [
            'session_type' => 'work'
        ]);

        $response->assertRedirect(route('staff.meeting-requests.create'))
                ->assertSessionHas('info');
    }

    /**
     * Test manual clock out functionality
     */
    public function test_manual_clock_out_success(): void
    {
        $this->actingAs($this->user);

        // Create active session
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => now()->subMinutes(10),
            'work_date' => today(),
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => true
        ]);

        // Clock out
        $response = $this->post('/staff/attendance/clock-out', [
            'notes' => 'Test clock out'
        ]);

        $response->assertRedirect()
                ->assertSessionHas('success');

        $attendance->refresh();

        $this->assertNotNull($attendance->clock_out);
        $this->assertNotNull($attendance->session_duration);
        $this->assertGreaterThan(0, $attendance->session_duration);
    }

    /**
     * Test manual clock out without active session
     */
    public function test_manual_clock_out_no_active_session(): void
    {
        $this->actingAs($this->user);

        $response = $this->post('/staff/attendance/clock-out');

        $response->assertRedirect()
                ->assertSessionHas('error');
    }

    /**
     * Test cross-day session handling
     */
    public function test_cross_day_session_handling(): void
    {
        $this->actingAs($this->user);

        // Create a session that started yesterday
        $yesterday = Carbon::yesterday('Asia/Jakarta');
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => $yesterday->setTime(23, 30),
            'work_date' => $yesterday,
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => true
        ]);

        // Clock out today
        $response = $this->post('/staff/attendance/clock-out');

        $response->assertRedirect()
                ->assertSessionHas('success');

        // Verify cross-day session was split into 2 records
        $sessions = Attendance::where('user_id', $this->user->id)->get();
        $this->assertEquals(2, $sessions->count());
        $attendance->refresh();
        $this->assertNotNull($attendance->clock_out);
    }

    /**
     * Test session duration calculation
     */
    public function test_session_duration_calculation(): void
    {
        $this->actingAs($this->user);

        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => now()->subMinutes(10),
            'work_date' => today(),
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => true
        ]);

        // Clock out
        $this->post('/staff/attendance/clock-out');

        $attendance->refresh();
        
        // Duration should be at least 2 seconds
        $this->assertGreaterThanOrEqual(2, $attendance->session_duration);
        $this->assertGreaterThan(0, $attendance->getDurationInHours());
    }

    /**
     * Test multiple sessions in one day
     */
    public function test_multiple_sessions_per_day(): void
    {
        $this->actingAs($this->user);

        // First session
        Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => now()->subHours(3),
            'work_date' => today(),
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => true
        ]);
        $this->post('/staff/attendance/clock-out');

        // Second session
        Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => now()->subHour(),
            'work_date' => today(),
            'session_number' => 2,
            'session_type' => 'overtime',
            'is_active' => true
        ]);
        $this->post('/staff/attendance/clock-out');

        // Should have 2 completed sessions
        $sessions = Attendance::where('user_id', $this->user->id)
            ->where('is_active', false)
            ->get();

        $this->assertEquals(2, $sessions->count());
        $this->assertEquals(1, $sessions->first()->session_number);
        $this->assertEquals(2, $sessions->last()->session_number);
    }

    /**
     * Test automatic clock in via API
     */
    public function test_automatic_clock_in_api(): void
    {
        $apiKey = 'test-api-key-123';
        config(['app.api_key' => $apiKey]);

        $response = $this->postJson('/api/absensi', [
            'player_id' => '12345',
            'player_name' => 'Test User',
            'clock_in' => now()->toDateTimeString()
        ], [
            'X-API-Key' => $apiKey
        ]);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true
                ]);

        $this->assertDatabaseHas('absensi', [
            'player_id' => '12345',
            'player_name' => 'Test User',
            'clock_out' => null
        ]);
    }

    /**
     * Test automatic clock out via API
     */
    public function test_automatic_clock_out_api(): void
    {
        $apiKey = 'test-api-key-123';
        config(['app.api_key' => $apiKey]);

        // First clock in
        $this->postJson('/api/absensi', [
            'player_id' => '12345',
            'player_name' => 'Test User',
            'clock_in' => now()->subHour()->toDateTimeString()
        ], [
            'X-API-Key' => $apiKey
        ]);

        // Then clock out
        $response = $this->postJson('/api/absensi', [
            'player_id' => '12345',
            'player_name' => 'Test User',
            'clock_in' => now()->subHour()->toDateTimeString(),
            'clock_out' => now()->toDateTimeString(),
            'time_on_duty' => '01:00:00'
        ], [
            'X-API-Key' => $apiKey
        ]);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true
                ]);

        $this->assertDatabaseHas('absensi', [
            'player_id' => '12345',
            'clock_out' => now()->toDateTimeString(),
            'time_on_duty' => '01:00:00'
        ]);
    }

    /**
     * Test API validation errors
     */
    public function test_api_validation_errors(): void
    {
        $apiKey = 'test-api-key-123';
        config(['app.api_key' => $apiKey]);

        $response = $this->postJson('/api/absensi', [
            'player_id' => '', // Empty player_id
            'player_name' => '', // Empty player_name
            'clock_in' => 'invalid-date' // Invalid date
        ], [
            'X-API-Key' => $apiKey
        ]);

        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'Validasi gagal'
                ])
                ->assertJsonStructure([
                    'errors' => [
                        'player_id',
                        'player_name',
                        'clock_in'
                    ]
                ]);
    }

    /**
     * Test API duplicate clock in prevention
     */
    public function test_api_duplicate_clock_in_prevention(): void
    {
        $apiKey = 'test-api-key-123';
        config(['app.api_key' => $apiKey]);

        // First clock in
        $this->postJson('/api/absensi', [
            'player_id' => '12345',
            'player_name' => 'Test User',
            'clock_in' => now()->toDateTimeString()
        ], [
            'X-API-Key' => $apiKey
        ]);

        // Try to clock in again
        $response = $this->postJson('/api/absensi', [
            'player_id' => '12345',
            'player_name' => 'Test User',
            'clock_in' => now()->toDateTimeString()
        ], [
            'X-API-Key' => $apiKey
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'is_duplicate' => true
                ]);
    }

    /**
     * Test integration between manual and automatic systems
     */
    public function test_manual_automatic_integration(): void
    {
        $this->actingAs($this->user);

        // Create manual attendance
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => now()->subHours(2),
            'work_date' => today(),
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => true
        ]);

        $apiKey = 'test-api-key-123';
        config(['app.api_key' => $apiKey]);

        // Send automatic attendance data
        $response = $this->postJson('/api/absensi', [
            'player_id' => '12345',
            'player_name' => 'Test User',
            'clock_in' => now()->subHour()->toDateTimeString(),
            'clock_out' => now()->toDateTimeString(),
            'time_on_duty' => '01:00:00'
        ], [
            'X-API-Key' => $apiKey
        ]);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'priority' => 'automatic'
                ]);

        // Check that manual record was updated
        $attendance->refresh();
        $this->assertNotNull($attendance->clock_out);
        $this->assertFalse($attendance->is_active);
    }

    /**
     * Test on-duty players API
     */
    public function test_on_duty_players_api(): void
    {
        $apiKey = 'test-api-key-123';
        config(['app.api_key' => $apiKey]);

        // Create some test data
        Absensi::create([
            'player_id' => '12345',
            'player_name' => 'Test User 1',
            'clock_in' => now()->subHour(),
            'clock_out' => null
        ]);

        Absensi::create([
            'player_id' => '67890',
            'player_name' => 'Test User 2',
            'clock_in' => now()->subHour(),
            'clock_out' => now()->subMinutes(30),
            'time_on_duty' => '00:30:00'
        ]);

        $response = $this->getJson('/api/absensi/on-duty', [
            'X-API-Key' => $apiKey
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true
                ])
                ->assertJsonStructure([
                    'data' => [
                        'total_on_duty',
                        'players' => [
                            '*' => [
                                'player_id',
                                'player_name',
                                'clock_in',
                                'duration'
                            ]
                        ]
                    ]
                ]);

        // Should only return 1 player (the one still on duty)
        $this->assertEquals(1, $response->json('data.total_on_duty'));
    }

    /**
     * Test attendance report API
     */
    public function test_attendance_report_api(): void
    {
        $apiKey = 'test-api-key-123';
        config(['app.api_key' => $apiKey]);

        // Create test data
        Absensi::create([
            'player_id' => '12345',
            'player_name' => 'Test User',
            'clock_in' => now()->subDay(),
            'clock_out' => now()->subDay()->addHour(),
            'time_on_duty' => '01:00:00'
        ]);

        $response = $this->getJson('/api/absensi/report/12345', [
            'X-API-Key' => $apiKey
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true
                ])
                ->assertJsonStructure([
                    'data' => [
                        'player_id',
                        'total_work_days',
                        'total_work_time',
                        'records'
                    ]
                ]);
    }

    /**
     * Test session splitting for cross-day sessions
     */
    public function test_cross_day_session_splitting(): void
    {
        $this->actingAs($this->user);

        // Create a cross-day session
        $yesterday = Carbon::yesterday('Asia/Jakarta');
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => $yesterday->setTime(23, 30),
            'clock_out' => now()->setTime(1, 30),
            'work_date' => $yesterday,
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => false,
            'session_duration' => 7200 // 2 hours in seconds
        ]);

        // Trigger splitting
        $attendance->splitCrossDaySession();

        // Should have 2 records now
        $sessions = Attendance::where('user_id', $this->user->id)
            ->orderBy('work_date')
            ->get();

        $this->assertEquals(2, $sessions->count());
        $this->assertEquals($yesterday->toDateString(), $sessions->first()->work_date->toDateString());
        $this->assertEquals(today()->toDateString(), $sessions->last()->work_date->toDateString());
    }

    /**
     * Test session validation and error handling
     */
    public function test_session_validation_and_error_handling(): void
    {
        $this->actingAs($this->user);

        // Test notes too long on clock out
        $response = $this->post('/staff/attendance/clock-out', [
            'notes' => str_repeat('a', 1001)
        ]);

        $response->assertSessionHasErrors(['notes']);
    }

    /**
     * Test timezone handling
     */
    public function test_timezone_handling(): void
    {
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => now(),
            'work_date' => today(),
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => true
        ]);

        // Verify timezone is Asia/Jakarta
        $this->assertEquals('Asia/Jakarta', $attendance->clock_in->timezone->getName());
    }

    /**
     * Test session duration edge cases
     */
    public function test_session_duration_edge_cases(): void
    {
        $this->actingAs($this->user);

        // Test very short session
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => now()->subSecond(),
            'work_date' => today(),
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => true
        ]);

        $this->post('/staff/attendance/clock-out');

        $attendance->refresh();
        $this->assertGreaterThan(0, $attendance->session_duration);
    }
}
