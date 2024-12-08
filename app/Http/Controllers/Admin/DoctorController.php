<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class DoctorController extends Controller
{
    public function index()
    {
        $doctors = Doctor::with('user')->paginate(10);
        return view('admin.doctors.index', compact('doctors'));
    }

    public function create()
    {
        return view('admin.doctors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date'],
            'specialization' => ['required', 'string', 'max:255'],
            'qualifications' => ['required', 'string', 'max:255'],
            'experience' => ['required', 'string'],
            'working_hours' => ['required', 'array']
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'doctor',
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'date_of_birth' => $validated['date_of_birth'],
        ]);

        Doctor::create([
            'user_id' => $user->id,
            'specialization' => $validated['specialization'],
            'qualifications' => $validated['qualifications'],
            'experience' => $validated['experience'],
            'is_available' => true,
            'working_hours' => json_encode($validated['working_hours'])
        ]);

        return redirect()->route('admin.doctors.index')
            ->with('success', 'Doctor added successfully');
    }
} 