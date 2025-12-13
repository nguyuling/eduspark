<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:student,teacher'],
            'district' => ['required', 'string', 'max:100'],
            'school_code' => ['required', 'string', 'regex:/^J[A-Z]{2}\d{4}$/'],
            'phone' => ['nullable', 'string', 'max:15', 'regex:/^[\+]?[0-9\s\-\(\)]{7,}$/'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // Auto-generate user_id based on role and school_code
        $prefix = $data['role'] === 'teacher' ? 'G' : 'P';
        $base = "{$prefix}-{$data['school_code']}-";
        $suffix = \Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(3, '0123456789abcdef'));
        $user_id = $base . $suffix;

        // Avoid collision (very rare)
        $attempts = 0;
        while (User::where('user_id', $user_id)->exists() && $attempts < 5) {
            $suffix = \Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(3, '0123456789abcdef'));
            $user_id = $base . $suffix;
            $attempts++;
        }

        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'district' => $data['district'],
            'school_code' => $data['school_code'],
            'phone' => $data['phone'] ?? null,
            'user_id' => $user_id,
        ]);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $this->create($request->all());

        // DO NOT auto-login, redirect to login page instead
        return redirect($this->redirectPath())
            ->with('success', 'Account created successfully! Please log in.');
    }
}
