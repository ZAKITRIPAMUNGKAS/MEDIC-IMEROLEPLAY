<?php

namespace Tests\Feature;

use App\Models\StaffRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class TrainingProgramEditTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        $jsonPath = storage_path('app/training_programs.json');
        if (File::exists($jsonPath)) {
            File::delete($jsonPath);
        }

        parent::tearDown();
    }

    public function test_updated_training_program_is_reflected_on_registration_form(): void
    {
        $role = StaffRole::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'level' => 10,
            'permissions' => ['manage_users'],
        ]);

        $admin = User::factory()->create([
            'staff_id' => '77001',
            'name' => 'Admin Pelatihan',
            'email' => 'adminpelatihan@example.com',
            'role_id' => $role->id,
            'hospital' => 'alta',
            'is_active' => true,
            'status' => 'offline',
        ]);

        $updateResponse = $this->actingAs($admin)->post(route('portal.training.program.update', 'operasi'), [
            'title' => 'FORMULIR PENDAFTARAN PELATIHAN OPERASI FASE XIV',
            'organizer' => 'Divisi PND - Tim Bedah MOT',
            'desc' => "Pendaftaran dibuka 1-3 Desember 2026.\nPelaksanaan pada 5 Desember 2026.",
            'requirement' => 'Minimal Perawat',
            'badge' => 'FASE XIV',
            'is_active' => '1',
        ]);

        $updateResponse->assertRedirect();
        $updateResponse->assertSessionHas('success');

        $formResponse = $this->actingAs($admin)->get(route('portal.training.form', 'operasi'));

        $formResponse->assertStatus(200);
        $formResponse->assertSee('FORMULIR PENDAFTARAN PELATIHAN OPERASI FASE XIV');
        $formResponse->assertSee('Divisi PND - Tim Bedah MOT');
        $formResponse->assertSee('Pendaftaran dibuka 1-3 Desember 2026.');
        $formResponse->assertDontSee('FORMULIR PENDAFTARAN PELATIHAN OPERASI FASE XIII');
    }

    public function test_hyphenated_program_key_syncs_with_underscored_form_type(): void
    {
        $role = StaffRole::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'level' => 10,
            'permissions' => ['manage_users'],
        ]);

        $admin = User::factory()->create([
            'staff_id' => '77002',
            'name' => 'Admin Surat',
            'email' => 'adminsurat@example.com',
            'role_id' => $role->id,
            'hospital' => 'alta',
            'is_active' => true,
            'status' => 'offline',
        ]);

        $this->actingAs($admin)->post(route('portal.training.program.update', 'surat-menyurat'), [
            'title' => 'Formulir Surat Menyurat Gelombang XV',
            'organizer' => 'Divisi PND - Tata Naskah',
            'desc' => 'Jadwal terbaru pelatihan surat menyurat bulan Desember.',
            'requirement' => 'Minimal Co-Ass',
            'badge' => 'Gelombang XV',
            'is_active' => '1',
        ])->assertRedirect();

        $formResponse = $this->actingAs($admin)->get(route('portal.training.form', 'surat-menyurat'));

        $formResponse->assertStatus(200);
        $formResponse->assertSee('Formulir Surat Menyurat Gelombang XV');
        $formResponse->assertSee('Divisi PND - Tata Naskah');
        $formResponse->assertSee('Jadwal terbaru pelatihan surat menyurat bulan Desember.');
    }
}
