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
        return view('auth.portal', ['mode' => 'login']);
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

            // Webhook system removed for better performance

            // Clear intended URL to prevent redirect loop
            session()->forget('url.intended');

            // Log successful login and redirect
            \Log::info('Login successful, redirecting to dashboard', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role_id' => $user->role_id,
                'is_staff' => $user->isStaff(),
                'session_id' => $request->session()->getId()
            ]);

            // Redirect to dashboard dengan status 302 untuk memastikan redirect berfungsi
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
        // Hanya tampilkan role yang diizinkan untuk registrasi: trainee, perawat, co_ass, dokter_umum, dokter_spesialis
        $allowedRoles = ['trainee', 'perawat', 'co_ass', 'dokter_umum', 'dokter_spesialis'];
        $roles = StaffRole::whereIn('name', $allowedRoles)
            ->orderBy('level', 'asc')
            ->get();
        return view('auth.portal', compact('roles'))->with('mode', 'register');
    }

    public function register(Request $request)
    {
        // Validasi role_id harus salah satu dari role yang diizinkan
        $allowedRoles = ['trainee', 'perawat', 'co_ass', 'dokter_umum', 'dokter_spesialis'];
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
                        $fail('Hanya role Trainee, Perawat, Co-Ass, Dokter Umum, atau Dokter Spesialis yang dapat dipilih saat registrasi.');
                    }
                },
            ],
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
            'is_active' => true,
            'profile_image' => $profileImagePath,
        ]);

        // Webhook system removed for better performance

        return redirect()->route('staff.login')->with('success', 'Staf baru berhasil didaftarkan! Silakan login dengan email dan password yang telah dibuat.');
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
            'current_password' => 'nullable|string',
            'password' => 'nullable|string|min:8|confirmed',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Update name
        $user->name = $validated['name'];

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
}
