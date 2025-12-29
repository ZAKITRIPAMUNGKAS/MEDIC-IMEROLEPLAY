<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Absensi;
use App\Models\User;
use App\Models\Attendance;

class AbsensiApiTest extends TestCase
{
    use RefreshDatabase;

    protected $apiKey;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set up test API key
        $this->apiKey = 'test-api-key-123';
        config(['app.api_key' => $this->apiKey]);
        
        // Create test user
        $this->user = User::factory()->create([
            'staff_id' => '12345',
            'name' => 'Test User'
        ]);
    }

    /**
     * Test API key authentication
     */
    public function test_api_requires_valid_key(): void
    {
        $response = $this->postJson('/api/absensi', [
            'player_id' => '12345',
            'player_name' => 'Test User',
            'clock_in' => '2025-10-02 08:00:00'
        ]);

        $response->assertStatus(401)
                ->assertJson([
                    'success' => false,
                    'error_code' => 'INVALID_API_KEY'
                ]);
    }

    /**
     * Test successful clock in
     */
    public function test_successful_clock_in(): void
    {
        $response = $this->postJson('/api/absensi', [
            'player_id' => '12345',
            'player_name' => 'Test User',
            'clock_in' => '2025-10-02 08:00:00'
        ], [
            'X-API-Key' => $this->apiKey
        ]);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'message' => 'Data absensi berhasil disimpan'
                ]);

        $this->assertDatabaseHas('absensi', [
            'player_id' => '12345',
            'player_name' => 'Test User',
            'clock_in' => '2025-10-02 08:00:00',
            'clock_out' => null
        ]);
    }

    /**
     * Test successful clock out
     */
    public function test_successful_clock_out(): void
    {
        // First clock in
        $this->postJson('/api/absensi', [
            'player_id' => '12345',
            'player_name' => 'Test User',
            'clock_in' => '2025-10-02 08:00:00'
        ], [
            'X-API-Key' => $this->apiKey
        ]);

        // Then clock out
        $response = $this->postJson('/api/absensi', [
            'player_id' => '12345',
            'player_name' => 'Test User',
            'clock_in' => '2025-10-02 08:00:00',
            'clock_out' => '2025-10-02 17:00:00',
            'time_on_duty' => '09:00:00'
        ], [
            'X-API-Key' => $this->apiKey
        ]);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'message' => 'Data absensi berhasil diupdate'
                ]);

        $this->assertDatabaseHas('absensi', [
            'player_id' => '12345',
            'clock_out' => '2025-10-02 17:00:00',
            'time_on_duty' => '09:00:00'
        ]);
    }

    /**
     * Test validation errors
     */
    public function test_validation_errors(): void
    {
        $response = $this->postJson('/api/absensi', [
            'player_id' => '', // Empty player_id
            'player_name' => '', // Empty player_name
            'clock_in' => 'invalid-date' // Invalid date
        ], [
            'X-API-Key' => $this->apiKey
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
     * Test get on duty players
     */
    public function test_get_on_duty_players(): void
    {
        // Create some test data
        Absensi::create([
            'player_id' => '12345',
            'player_name' => 'Test User 1',
            'clock_in' => '2025-10-02 08:00:00',
            'clock_out' => null
        ]);

        Absensi::create([
            'player_id' => '67890',
            'player_name' => 'Test User 2',
            'clock_in' => '2025-10-02 09:00:00',
            'clock_out' => '2025-10-02 17:00:00',
            'time_on_duty' => '08:00:00'
        ]);

        $response = $this->getJson('/api/absensi/on-duty');

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
     * Test get attendance report
     */
    public function test_get_attendance_report(): void
    {
        // Create test data
        Absensi::create([
            'player_id' => '12345',
            'player_name' => 'Test User',
            'clock_in' => '2025-10-02 08:00:00',
            'clock_out' => '2025-10-02 17:00:00',
            'time_on_duty' => '09:00:00'
        ]);

        $response = $this->getJson('/api/absensi/report/12345');

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
     * Test rate limiting
     */
    public function test_rate_limiting(): void
    {
        // Make multiple requests quickly
        for ($i = 0; $i < 35; $i++) {
            $response = $this->postJson('/api/absensi', [
                'player_id' => '12345',
                'player_name' => 'Test User',
                'clock_in' => '2025-10-02 08:00:00'
            ], [
                'X-API-Key' => $this->apiKey
            ]);
        }

        // Should be rate limited
        $response->assertStatus(429)
                ->assertJson([
                    'success' => false,
                    'error_code' => 'RATE_LIMIT_EXCEEDED'
                ]);
    }

    /**
     * Test integration with manual attendance system
     */
    public function test_integration_with_manual_system(): void
    {
        // Create manual attendance record
        Attendance::create([
            'user_id' => $this->user->id,
            'clock_in' => '2025-10-02 08:00:00',
            'work_date' => '2025-10-02',
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => true
        ]);

        // Send automatic attendance data
        $response = $this->postJson('/api/absensi', [
            'player_id' => '12345',
            'player_name' => 'Test User',
            'clock_in' => '2025-10-02 08:00:00',
            'clock_out' => '2025-10-02 17:00:00',
            'time_on_duty' => '09:00:00'
        ], [
            'X-API-Key' => $this->apiKey
        ]);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'priority' => 'manual'
                ]);

        // Check that manual record was updated
        $this->assertDatabaseHas('attendances', [
            'user_id' => $this->user->id,
            'clock_out' => '2025-10-02 17:00:00',
            'is_active' => false
        ]);
    }
}
