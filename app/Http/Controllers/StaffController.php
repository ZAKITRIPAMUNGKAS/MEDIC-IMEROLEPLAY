<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StaffRole;
use App\Helpers\ImageHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class StaffController extends Controller
{
    public function showProfile()
    {
        return view('staff.profile');
    }
    public function showLoginForm()
    {
        // Restrict roles for the dropdown in the portal view
        $allowedRoles = ['trainee', 'perawat'];
        $roles = StaffRole::whereIn('name', $allowedRoles)->orderBy('level', 'asc')->get();

        return view('auth.portal', [
            'mode' => 'login',
            'roles' => $roles
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        // Force remember-me to keep users logged in across sessions
        $remember = true;

        // Log login attempt
        \Log::info('Login attempt', [
            'email' => $request->email,
            'ip' => $request->ip(),
            'csrf_token' => $request->session()->token(),
            'session_id' => $request->session()->getId()
        ]);

        if (Auth::attempt($credentials, $remember)) {
            // Regenerate session ID untuk keamanan
            $request->session()->regenerate();

            $user = Auth::user();

            // Log successful authentication
            \Log::info('Auth successful', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role_id' => $user->role_id
            ]);

            // Check if user is staff
            if (!Auth::user()->isStaff()) {
                \Log::warning('User is not staff', ['user_id' => $user->id, 'role_id' => $user->role_id]);
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => 'Akun ini bukan akun staf.',
                ]);
            }

            // Check if user is active
            if (!$user->is_active) {
                \Log::warning('Inactive user tried to login', ['user_id' => $user->id]);
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => 'Akun Anda belum aktif. Silakan hubungi Admin/HRD untuk aktivasi.',
                ]);
            }

            // Webhook system removed for better performance

            // Clear intended URL to prevent redirect loop
            session()->forget('url.intended');

            // Check if user has viewed wrapped for current year
            $currentYear = now()->year;
            $hasViewedWrapped = \App\Models\UserWrappedView::hasViewed($user->id, $currentYear);

            // Log successful login and redirect destination
            \Log::info('Login successful, checking wrapped status', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role_id' => $user->role_id,
                'is_staff' => $user->isStaff(),
                'has_viewed_wrapped' => $hasViewedWrapped,
                'year' => $currentYear,
                'session_id' => $request->session()->getId()
            ]);

            // Redirect to wrapped if not viewed yet, otherwise to dashboard
            if (!$hasViewedWrapped) {
                return redirect()->route('wrapped.show', ['year' => $currentYear])
                    ->with('success', 'Login berhasil! Lihat rekap tahun kamu 🎉')
                    ->with('login_time', now()->format('Y-m-d H:i:s'));
            }

            // Redirect to dashboard if already viewed wrapped
            return redirect()->route('staff.dashboard')
                ->with('success', 'Login berhasil!')
                ->with('login_time', now()->format('Y-m-d H:i:s'));
        }

        \Log::warning('Login failed', [
            'email' => $request->email,
            'ip' => $request->ip()
        ]);

        throw ValidationException::withMessages([
            'email' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
        ]);
    }

    public function logout(Request $request)
    {
        // Webhook logout sebelum session direset
        // Webhook system removed for better performance

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('public.index');
    }

    public function showRegisterForm()
    {
        // Hanya tampilkan role yang diizinkan untuk registrasi: trainee, perawat, co_ass
        $allowedRoles = ['trainee', 'perawat'];
        $roles = StaffRole::whereIn('name', $allowedRoles)
            ->orderBy('level', 'asc')
            ->get();
        return view('auth.portal', compact('roles'))->with('mode', 'register');
    }

    public function register(Request $request)
    {
        // Validasi role_id harus salah satu dari role yang diizinkan
        $allowedRoles = ['trainee', 'perawat'];
        $allowedRoleIds = StaffRole::whereIn('name', $allowedRoles)->pluck('id')->toArray();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => [
                'required',
                'exists:staff_roles,id',
                function ($attribute, $value, $fail) use ($allowedRoleIds) {
                    if (!in_array($value, $allowedRoleIds)) {
                        $fail('Hanya role Trainee dan Perawat yang dapat dipilih saat registrasi.');
                    }
                },
            ],
            'hospital' => 'required|in:alta,roxwood',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $profileImagePath = null;
        if ($request->hasFile('profile_image')) {
            $profileImagePath = $request->file('profile_image')->store('profile-images', 'public');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'hospital' => $request->hospital,
            'is_active' => false,
            'profile_image' => $profileImagePath,
        ]);

        // Webhook system removed for better performance

        return redirect()->route('staff.login')->with('success', 'Registrasi berhasil! Akun Anda menunggu aktivasi dari Admin/HRD sebelum bisa login.');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        \Log::info('Profile update request received', [
            'user_id' => $user->id,
            'has_file' => $request->hasFile('profile_image'),
            'file_valid' => $request->hasFile('profile_image') ? $request->file('profile_image')->isValid() : false,
            'all_files' => $request->allFiles(),
            'request_data' => $request->except(['_token', 'password', 'current_password']),
            'upload_errors' => $request->hasFile('profile_image') ? $request->file('profile_image')->getError() : 'No file',
            'upload_error_message' => $request->hasFile('profile_image') ? $request->file('profile_image')->getErrorMessage() : 'No file'
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'hospital' => 'required|in:alta,roxwood',
            'current_password' => 'nullable|string',
            'password' => 'nullable|string|min:8|confirmed',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Update name and hospital
        $user->name = $validated['name'];
        $user->hospital = $validated['hospital'];

        // If user wants to change password, verify current password (if set) then update
        if (!empty($validated['password'])) {
            if (empty($validated['current_password']) || !Hash::check($validated['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.'])->withInput();
            }
            $user->password = Hash::make($validated['password']);
        }

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            \Log::info('Profile image upload started', [
                'user_id' => $user->id,
                'file_name' => $request->file('profile_image')->getClientOriginalName(),
                'file_size' => $request->file('profile_image')->getSize(),
                'current_profile_image' => $user->profile_image
            ]);

            // Hapus gambar lama jika ada
            if ($user->profile_image) {
                $oldImagePath = null;

                // Check if it's a storage path or public path
                if (str_starts_with($user->profile_image, 'uploads/')) {
                    // Public path
                    $oldImagePath = public_path($user->profile_image);
                } else {
                    // Storage path
                    $oldImagePath = storage_path('app/public/' . $user->profile_image);
                }

                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                    \Log::info('Old profile image deleted', ['path' => $oldImagePath]);
                }
            }

            // For hosting compatibility, always use public directory
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $request->file('profile_image')->getClientOriginalName());
            $publicPath = public_path('uploads/profile-images');
            $destinationPath = $publicPath . '/' . $fileName;

            // Create directory if it doesn't exist
            if (!is_dir($publicPath)) {
                mkdir($publicPath, 0755, true);
            }

            // Compress and save image (max 500x500, quality 85)
            $compressed = ImageHelper::compressUploadedImage(
                $request->file('profile_image'),
                $destinationPath,
                500, // max width
                500, // max height
                85   // quality
            );

            // If compression failed, use original file
            if (!$compressed) {
                $request->file('profile_image')->move($publicPath, $fileName);
            }

            $user->profile_image = 'uploads/profile-images/' . $fileName;

            \Log::info('Profile image uploaded to public directory', [
                'path' => $user->profile_image,
                'full_path' => public_path($user->profile_image),
                'file_exists' => file_exists(public_path($user->profile_image))
            ]);
        }

        $user->save();

        // Update session name immediately without re-login
        $request->session()->put('auth_name', $user->name);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updateEmail(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|string',
            'new_email' => 'required|string|email|max:255|unique:users,email|confirmed',
        ]);

        // Verifikasi password saat ini
        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Password saat ini tidak sesuai.',
            ]);
        }

        $oldEmail = $user->email;
        $user->email = $request->new_email;
        $user->save();

        \Log::info('Staff email updated', [
            'user_id' => $user->id,
            'old_email' => $oldEmail,
            'new_email' => $user->email
        ]);

        return back()->with('info', 'Email berhasil diperbarui. Silakan login kembali dengan email baru jika diperlukan.');
    }
}
