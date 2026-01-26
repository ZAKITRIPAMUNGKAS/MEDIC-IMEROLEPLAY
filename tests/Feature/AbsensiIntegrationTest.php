<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Absensi;
use App\Models\Attendance;

class AbsensiIntegrationTest extends TestCase
{
    // WARNING: We are testing on a real database, so we won't use RefreshDatabase to avoid wiping data
    // We will clean up our test data manualy

    public function test_api_absensi_can_receive_data_and_integrate()
    {
        // 1. Create a Test User
        $user = User::where('email', 'test.integration@example.com')->first();
        if (!$user) {
            $user = User::factory()->create([
                'name' => 'Test Integration Agent',
                'email' => 'test.integration@example.com',
                'staff_id' => 'steam:test12345',
                'password' => bcrypt('password'),
            ]);
        }

        // 2. Prepare Data
        $data = [
            'player_id' => 'steam:test12345',
            'player_name' => 'Test Integration Agent',
            'clock_in' => now()->subMinutes(10)->format('Y-m-d H:i:s'),
            'clock_out' => null,
            'time_on_duty' => null
        ];

        // 3. Send Request
        $response = $this->postJson('/api/absensi', $data);

        // 4. Assertions
        $response->assertStatus(201);
        $response->assertJson(['success' => true]);

        // 5. Verify Database - Absensi (FiveM Record)
        $this->assertDatabaseHas('absensi', [
            'player_id' => $data['player_id'],
            'source' => 'automatic' // Default
        ]);

        // 6. Verify Database - Attendance (Manual Record via Integration Service)
        // Check if correct source is set
        $attendance = Attendance::where('user_id', $user->id)
            ->where('clock_in', $data['clock_in'])
            ->first();

        $this->assertNotNull($attendance, 'Attendance record not created');
        $this->assertEquals('fivem', $attendance->source, 'Source column not set to fivem');

        // Cleanup
        if ($attendance)
            $attendance->delete();
        Absensi::where('player_id', $data['player_id'])->delete();
        // $user->delete(); // Keep user for inspection if needed, or delete
    }
}
