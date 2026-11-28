<?php

namespace Tests\Feature;

use App\Models\StaffRole;
use App\Models\TrainingApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PndTrainingDeleteAndDeduplicationTest extends TestCase
{
    use RefreshDatabase;

    private function createStaffUser(string $roleName = 'perawat', int $level = 1): User
    {
        $role = StaffRole::create([
            'name' => $roleName,
            'display_name' => ucfirst($roleName),
            'level' => $level,
            'permissions' => $roleName === 'admin' ? ['manage_users'] : [],
        ]);

        return User::factory()->create([
            'staff_id' => (string) random_int(10000, 99999),
            'citizen_id' => (string) random_int(10000, 99999),
            'name' => 'Staff ' . $roleName,
            'email' => $roleName . '-' . uniqid() . '@example.com',
            'role_id' => $role->id,
            'hospital' => 'alta',
            'is_active' => true,
            'status' => 'offline',
        ]);
    }

    public function test_duplicate_pending_training_submission_does_not_stack(): void
    {
        $user = $this->createStaffUser('perawat', 1);

        // First submission succeeds
        $first = $this->actingAs($user)->post(route('portal.training.submit', 'operasi'), [
            'nama_ic' => 'Xporcut get',
            'gender' => 'Laki-laki',
            'batch' => 'BATCH XV',
        ]);

        $first->assertRedirect(route('portal.training.index'));
        $first->assertSessionHas('success');
        $this->assertSame(1, TrainingApplication::where('user_id', $user->id)->count());

        // Second submission for same training + pending batch is blocked
        $second = $this->actingAs($user)->post(route('portal.training.submit', 'operasi'), [
            'nama_ic' => 'Xporcut get',
            'gender' => 'Laki-laki',
            'batch' => 'batch xv',
        ]);

        $second->assertRedirect(route('portal.training.index'));
        $second->assertSessionHas('error');
        $this->assertSame(1, TrainingApplication::where('user_id', $user->id)->count());

        // Rapid submission even with a different batch within 15s is also blocked
        $rapidOtherBatch = $this->actingAs($user)->post(route('portal.training.submit', 'operasi'), [
            'nama_ic' => 'Xporcut get',
            'gender' => 'Laki-laki',
            'batch' => 'BATCH XVI',
        ]);

        $rapidOtherBatch->assertRedirect(route('portal.training.index'));
        $rapidOtherBatch->assertSessionHas('error');
        $this->assertSame(1, TrainingApplication::where('user_id', $user->id)->count());
    }

    public function test_pnd_can_single_delete_and_bulk_delete_training_applications(): void
    {
        $admin = $this->createStaffUser('admin', 10);
        $applicant = $this->createStaffUser('perawat', 1);

        $app1 = TrainingApplication::create([
            'user_id' => $applicant->id,
            'training_type' => TrainingApplication::TYPE_OPERASI,
            'nama_ic' => 'Xporcut get',
            'gender' => 'Laki-laki',
            'batch' => 'BATCH XV',
            'status' => TrainingApplication::STATUS_PENDING,
        ]);

        $app2 = TrainingApplication::create([
            'user_id' => $applicant->id,
            'training_type' => TrainingApplication::TYPE_OPERASI,
            'nama_ic' => 'Xporcut get',
            'gender' => 'Laki-laki',
            'batch' => 'BATCH XV',
            'status' => TrainingApplication::STATUS_PENDING,
        ]);

        $app3 = TrainingApplication::create([
            'user_id' => $applicant->id,
            'training_type' => TrainingApplication::TYPE_OPERASI,
            'nama_ic' => 'Xporcut get',
            'gender' => 'Laki-laki',
            'batch' => 'BATCH XV',
            'status' => TrainingApplication::STATUS_PENDING,
        ]);

        // Index page renders non-AJAX bulkForm, deleteForm, and proper destroy URL
        $indexResponse = $this->actingAs($admin)->get(route('portal.pnd.training.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('id="bulkForm"', false);
        $indexResponse->assertSee('id="bulkActionInput"', false);
        $indexResponse->assertSee('data-delete-url="' . route('portal.pnd.training.destroy', $app1->id) . '"', false);

        // Single delete removes app1
        $singleDelete = $this->actingAs($admin)->delete(route('portal.pnd.training.destroy', $app1->id));
        $singleDelete->assertRedirect();
        $singleDelete->assertSessionHas('success');
        $this->assertDatabaseMissing('training_applications', ['id' => $app1->id]);

        // Deleting an already-deleted ID handles gracefully without 404
        $duplicateDelete = $this->actingAs($admin)->delete(route('portal.pnd.training.destroy', $app1->id));
        $duplicateDelete->assertRedirect();
        $duplicateDelete->assertSessionHas('info');

        // Bulk delete removes remaining items even if one ID in list was already deleted
        $bulkDelete = $this->actingAs($admin)->post(route('portal.pnd.training.bulk'), [
            'action' => 'delete',
            'ids' => [$app1->id, $app2->id, $app3->id],
        ]);
        $bulkDelete->assertRedirect();
        $bulkDelete->assertSessionHas('success');
        $this->assertDatabaseMissing('training_applications', ['id' => $app2->id]);
        $this->assertDatabaseMissing('training_applications', ['id' => $app3->id]);
    }
}
