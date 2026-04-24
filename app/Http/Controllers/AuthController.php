<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'These credentials do not match our records.',
            ])->onlyInput('email');
        }

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            return back()->withErrors(['email' => 'Your account has been deactivated.']);
        }

        $request->session()->regenerate();

        return redirect()->intended($this->redirectAfterLogin($user->role));
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'phone'    => ['nullable', 'string', 'max:20'],
            'dob'      => ['nullable', 'date', 'before:today'],
            'gender'   => ['nullable', 'in:male,female,other'],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'patient',
        ]);

        // Auto-create patient profile
        Patient::create([
            'user_id'        => $user->id,
            'patient_number' => Patient::generatePatientNumber(),
            'phone'          => $data['phone'] ?? null,
            'date_of_birth'  => $data['dob'] ?? null,
            'gender'         => $data['gender'] ?? null,
        ]);

        Auth::login($user);

        return redirect()->route('patient.dashboard')
                         ->with('success', 'Welcome to CityCare! Your account has been created.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'You have been logged out.');
    }

    private function redirectAfterLogin(string $role): string
    {
        return match($role) {
            'admin'         => route('admin.dashboard'),
            'receptionist'  => route('receptionist.dashboard'),
            'doctor'        => route('doctor.dashboard'),
            'cashier'       => route('cashier.dashboard'),
            'patient'       => route('patient.dashboard'),
            default         => '/',
        };
    }
}
