<?php

namespace Tests\Feature;

use App\Models\LeaveRequest;
use App\Models\StaffRole;
use App\Models\StaffSubRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IeLeaveDeleteTest extends TestCase
{
    use RefreshDatabase;

    private function createUserWithDivision(?string $subRoleName = null, string $roleName = 'perawat', int $level = 1, string $hospital = 'alta'): User
    {
        $role = StaffRole::firstOrCreate(
            ['name' => $roleName],
            ['display_name' => ucfirst($roleName), 'level' => $level, 'permissions' => []]
        );

        $subRoleId = null;
        if ($subRoleName) {
            $subRole = StaffSubRole::firstOrCreate(
                ['name' => $subRoleName],
                ['display_name' => strtoupper($subRoleName)]
            );
            $subRoleId = $subRole->id;
        }

        return User::factory()->create([
            'staff_id' => (string) random_int(10000, 99999),
            'citizen_id' => (string) random_int(10000, 99999),
            'name' => 'Staff ' . ($subRoleName ?? $roleName),
            'email' => uniqid('user_') . '@example.com',
            'role_id' => $role->id,
            'sub_role_id' => $subRoleId,
            'hospital' => $hospital,
            'is_active' => true,
            'status' => 'offline',
        ]);
    }

    private function createLeaveFor(User $applicant): LeaveRequest
    {
        return LeaveRequest::create([
            'user_id' => $applicant->id,
            'letter_date' => now()->toDateString(),
            'subject' => 'Izin Cuti',
            'recipient' => 'Yth. Direktur IME Medical Center',
            'applicant_name' => $applicant->name,
            'position' => 'Perawat',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'duration_days' => 3,
            'reason_ic' => 'Urusan keluarga',
            'reason_ooc' => 'Sibuk IRL',
            'status' => 'approved',
            'approved_at' => now(),
        ]);
    }

    public function test_ie_can_view_delete_controls_and_delete_single_or_bulk_leave_requests(): void
    {
        $ieUser = $this->createUserWithDivision('ie', 'perawat', 1);
        $staff = $this->createUserWithDivision(null, 'perawat', 1);

        $leave1 = $this->createLeaveFor($staff);
        $leave2 = $this->createLeaveFor($staff);
        $leave3 = $this->createLeaveFor($staff);

        // IE manage page shows delete controls
        $manageResponse = $this->actingAs($ieUser)->get(route('portal.leave.manage'));
        $manageResponse->assertStatus(200);
        $manageResponse->assertSee('id="leaveBulkDeleteForm"', false);
        $manageResponse->assertSee('data-delete-url="' . route('portal.leave.destroy', $leave1) . '"', false);

        // Single delete by IE
        $singleDelete = $this->actingAs($ieUser)->delete(route('portal.leave.destroy', $leave1));
        $singleDelete->assertRedirect();
        $singleDelete->assertSessionHas('success');
        $this->assertDatabaseMissing('leave_requests', ['id' => $leave1->id]);

        // Bulk delete by IE
        $bulkDelete = $this->actingAs($ieUser)->post(route('portal.leave.bulk-destroy'), [
            'ids' => [$leave2->id, $leave3->id],
        ]);
        $bulkDelete->assertRedirect();
        $bulkDelete->assertSessionHas('success');
        $this->assertDatabaseMissing('leave_requests', ['id' => $leave2->id]);
        $this->assertDatabaseMissing('leave_requests', ['id' => $leave3->id]);
    }

    public function test_non_ie_regular_staff_cannot_delete_leave_requests(): void
    {
        $regularStaff = $this->createUserWithDivision(null, 'perawat', 1);
        $leave = $this->createLeaveFor($regularStaff);

        $this->actingAs($regularStaff)
            ->delete(route('portal.leave.destroy', $leave))
            ->assertStatus(403);

        $this->actingAs($regularStaff)
            ->post(route('portal.leave.bulk-destroy'), ['ids' => [$leave->id]])
            ->assertStatus(403);

        $this->assertDatabaseHas('leave_requests', ['id' => $leave->id]);
    }
}
