<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    // 🔹 UPDATED: Register — now with phone, district, school_code & auto user_id
    public function register(Request $request)
    {
        // ✅ Enhanced validation (inline — no extra Request class needed)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:student,teacher', // note: 'student', not 'pelajar'
            'district' => 'required|string|max:100',
            'school_code' => [
                'required',
                'string',
                'regex:/^J[A-Z]{2}\d{4}$/',
            ],
            'phone' => 'nullable|string|max:15|regex:/^[\+]?[0-9\s\-\(\)]{7,}$/',
        ]);

        // ✅ Auto-generate user_id (same logic as model boot, but safe here too)
        $prefix = $validated['role'] === 'teacher' ? 'G' : 'P';
        $base = "{$prefix}-{$validated['school_code']}-";
        $suffix = Str::lower(Str::random(3, '0123456789abcdef'));
        $user_id = $base . $suffix;

        // Avoid collision (very rare)
        $attempts = 0;
        while (User::where('user_id', $user_id)->exists() && $attempts < 5) {
            $suffix = Str::lower(Str::random(3, '0123456789abcdef'));
            $user_id = $base . $suffix;
            $attempts++;
        }

        // ✅ Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'district' => $validated['district'],
            'school_code' => $validated['school_code'],
            'phone' => $validated['phone'] ?? null,
            'user_id' => $user_id, // ✅ now explicitly set
        ]);

        return redirect('/register')
            ->with('success', "Account succesfully created! You can now log in.");
    }

    // 🔹 Login (unchanged — keeps your JSON API behavior)
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ]);
        }

        auth()->login($user);

        return response()->json([
            'success' => true,
            'redirect' => url('/profile')
        ]);
    }

    // 🔹 Show main profile page
    public function profile()
    {
        $user = auth()->user();
        return view('user.profile.show', compact('user'));
    }

    // 🔹 Edit main profile (name/email)
    public function editProfile()
    {
        $user = auth()->user();
        return view('user.profile.edit', compact('user'));
    }

    // 🔹 Update name/email
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
        ]);

        if ($request->filled('name')) {
            $user->name = $request->name;
        }
        if ($request->filled('email')) {
            $user->email = $request->email;
        }

        $user->save();

        return back()->with('success', 'Profile information updated successfully.');
    }

    // 🔹 Show password change form
    public function editPassword()
    {
        return view('user.profile.edit-password');
    }

    // 🔹 Update password securely
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!Hash::check($value, auth()->user()->password)) {
                        $fail('The current password is incorrect.');
                    }
                },
            ],
            'password' => 'required|string|min:6|confirmed',
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password)
        ]);

        // ✅ SAFE & EXPLICIT REDIRECT
        return redirect()->route('profile.show')
            ->with('success', 'Your password has been updated successfully.');
    }
}