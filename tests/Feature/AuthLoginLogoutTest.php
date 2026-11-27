<?php

namespace Tests\Feature;

use App\Models\StaffRole;
use App\Models\User;
use App\Models\UserWrappedView;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class AuthLoginLogoutTest extends TestCase
{
    use RefreshDatabase;

    private function createActiveStaffUser(): User
    {
        $role = StaffRole::create([
            'name' => 'perawat',
            'display_name' => 'Perawat',
            'level' => 1,
            'permissions' => [],
        ]);

        return User::factory()->create([
            'staff_id' => '90001',
            'citizen_id' => '90001',
            'name' => 'Ns. Auth Tester',
            'email' => 'auth-tester@example.com',
            'password' => Hash::make('secret1234'),
            'role_id' => $role->id,
            'hospital' => 'alta',
            'is_active' => true,
            'status' => 'offline',
        ]);
    }

    public function test_login_page_sets_no_store_headers_and_csrf_refresh_hooks(): void
    {
        $response = $this->get(route('staff.login'));

        $response->assertStatus(200);
        $cacheControl = (string) $response->headers->get('Cache-Control');
        $this->assertStringContainsString('no-store', $cacheControl);
        $this->assertStringContainsString('no-cache', $cacheControl);
        $response->assertSee('name="csrf-token"', false);
        $response->assertSee('refreshPortalCsrfToken', false);
    }

    public function test_staff_can_login_logout_and_login_again_without_error(): void
    {
        $user = $this->createActiveStaffUser();

        UserWrappedView::create([
            'user_id' => $user->id,
            'year' => now()->year,
            'viewed_at' => now(),
            'session_id' => 'seed-session',
        ]);

        // First login
        $loginResponse = $this->post(route('staff.login.post'), [
            'email' => $user->email,
            'password' => 'secret1234',
        ]);

        $loginResponse->assertRedirect(route('staff.dashboard'));
        $this->assertAuthenticatedAs($user);

        // Logout
        $logoutResponse = $this->post(route('staff.logout'));
        $logoutResponse->assertRedirect(route('public.index'));
        $this->assertGuest();

        // Login again right after logout
        $loginPageAgain = $this->get(route('staff.login'));
        $loginPageAgain->assertStatus(200);

        $secondLoginResponse = $this->post(route('staff.login.post'), [
            'email' => $user->email,
            'password' => 'secret1234',
        ]);

        $secondLoginResponse->assertRedirect(route('staff.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_logout_works_when_already_logged_out_or_non_staff_or_via_get(): void
    {
        // Guest POST /staff/logout (e.g. session expired before clicking logout)
        $guestLogout = $this->post(route('staff.logout'));
        $guestLogout->assertRedirect(route('public.index'));

        // Non-staff user POST /staff/logout (must not 403)
        $nonStaffUser = User::factory()->create([
            'staff_id' => '90002',
            'citizen_id' => '90002',
            'name' => 'Warga Biasa',
            'email' => 'citizen@example.com',
            'role_id' => null,
            'is_active' => true,
        ]);

        $nonStaffLogout = $this->actingAs($nonStaffUser)->post(route('staff.logout'));
        $nonStaffLogout->assertRedirect(route('public.index'));
        $this->assertGuest();

        // GET /staff/logout must not throw 405 Method Not Allowed
        $getLogoutGuest = $this->get('/staff/logout');
        $getLogoutGuest->assertRedirect(route('public.index'));

        $staffUser = $this->createActiveStaffUser();
        $getLogoutStaff = $this->actingAs($staffUser)->get('/staff/logout');
        $getLogoutStaff->assertRedirect(route('staff.dashboard'));
    }

    public function test_authenticated_staff_visiting_login_redirects_to_dashboard(): void
    {
        $user = $this->createActiveStaffUser();

        $response = $this->actingAs($user)->get(route('staff.login'));
        $response->assertRedirect(route('staff.dashboard'));
    }

    public function test_token_mismatch_on_logout_and_login_never_returns_419_html_error_page(): void
    {
        $handler = app(ExceptionHandler::class);
        $session = app('session.store');
        $session->start();

        // 1. Stale CSRF on POST /staff/logout when guest -> clean redirect to home
        $logoutRequest = Request::create('/staff/logout', 'POST');
        $logoutRequest->setLaravelSession($session);

        $logoutResponse = $handler->render($logoutRequest, new TokenMismatchException('CSRF token mismatch.'));
        $this->assertSame(302, $logoutResponse->getStatusCode());
        $this->assertSame(route('public.index'), $logoutResponse->headers->get('Location'));

        // 2. Stale CSRF on POST /staff/logout when still authenticated -> redirect to dashboard with error flash
        $user = $this->createActiveStaffUser();
        $this->be($user);
        $authLogoutResponse = $handler->render($logoutRequest, new HttpException(419, 'CSRF token mismatch.'));
        $this->assertSame(302, $authLogoutResponse->getStatusCode());
        $this->assertSame(route('staff.dashboard'), $authLogoutResponse->headers->get('Location'));
        auth()->logout();

        // 3. Stale CSRF on POST /staff/login -> redirect back to login with message & email input (no password)
        $loginRequest = Request::create('/staff/login', 'POST', [
            'email' => $user->email,
            'password' => 'secret1234',
        ]);
        $loginRequest->setLaravelSession($session);

        $loginResponse = $handler->render($loginRequest, new TokenMismatchException('CSRF token mismatch.'));
        $this->assertSame(302, $loginResponse->getStatusCode());
        $this->assertSame(route('staff.login'), $loginResponse->headers->get('Location'));
        $this->assertSame($user->email, $session->getOldInput('email'));
        $this->assertNull($session->getOldInput('password'));

        // 4. AJAX/JSON 419 returns JSON with fresh csrf_token
        $jsonRequest = Request::create('/staff/login', 'POST', [], [], [], [
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
        ]);
        $jsonRequest->setLaravelSession($session);

        $jsonResponse = $handler->render($jsonRequest, new TokenMismatchException('CSRF token mismatch.'));
        $this->assertSame(419, $jsonResponse->getStatusCode());
        $payload = json_decode($jsonResponse->getContent(), true);
        $this->assertSame('TOKEN_MISMATCH', $payload['error']);
        $this->assertNotEmpty($payload['csrf_token']);
    }
}
