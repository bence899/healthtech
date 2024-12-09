<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Doctor;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        // Common validation rules
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['required', 'string', 'max:20'],
            'date_of_birth' => ['required', 'date'],
            'role' => ['required', 'string', 'in:patient,doctor,admin'],
        ];

        // Role-specific validation rules
        if ($request->role === 'doctor') {
            $rules['specialization'] = ['required', 'string', 'max:255'];
            $rules['qualifications'] = ['required', 'string', 'max:255'];
            $rules['experience'] = ['nullable', 'string'];
            $rules['address'] = ['required', 'string', 'max:255'];
        }

        if ($request->role === 'patient') {
            $rules['emergency_contact'] = ['required', 'string', 'max:20'];
            $rules['address'] = ['nullable', 'string', 'max:255'];
        }

        $validated = $request->validate($rules);

        // Create base user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'date_of_birth' => $validated['date_of_birth'],
            'role' => $validated['role'],
            'address' => $validated['address'] ?? null,
        ]);

        // Handle role-specific data
        if ($validated['role'] === 'doctor') {
            Doctor::create([
                'user_id' => $user->id,
                'specialization' => $validated['specialization'],
                'qualifications' => $validated['qualifications'],
                'experience' => $validated['experience'] ?? null,
                'is_available' => true,
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
