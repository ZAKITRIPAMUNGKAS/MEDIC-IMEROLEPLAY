<?php

namespace Tests\Feature;

use App\Models\StaffRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class AnatomyRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_anatomy_page(): void
    {
        $response = $this->get(route('staff.anatomy'));

        $response->assertRedirect('/login');
    }

    public function test_authenticated_staff_can_view_anatomy_iframe_page_and_navbar_links(): void
    {
        $role = StaffRole::create([
            'name' => 'paramedic',
            'display_name' => 'Paramedic',
            'permissions' => [],
        ]);

        $user = User::factory()->create([
            'staff_id' => '99881',
            'name' => 'Dr. Anatomy Tester',
            'email' => 'anatomy@example.com',
            'role_id' => $role->id,
            'is_active' => true,
            'status' => 'offline',
        ]);

        $response = $this->actingAs($user)->get(route('staff.anatomy'));

        $response->assertStatus(200);
        $response->assertSee('Anatomy Tubuh');
        $response->assertSee(route('staff.anatomy'), false);
        $response->assertSee('https://apotikmedicalcenter.my.id/anatomy/', false);
        $response->assertSee('allowfullscreen', false);
        $response->assertSee('toggleAnatomyFullscreen', false);
    }

    public function test_dashboard_sidebar_template_compiles_with_anatomy_link(): void
    {
        $bladeSource = file_get_contents(resource_path('views/staff/dashboard.blade.php'));

        $this->assertNotFalse($bladeSource);
        $this->assertStringContainsString("route('staff.anatomy')", $bladeSource);
        $this->assertStringContainsString('Anatomy Tubuh', $bladeSource);

        $compiled = Blade::compileString($bladeSource);
        $this->assertStringContainsString("route('staff.anatomy')", $compiled);
    }
}
