<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Illuminate\Http\Request;

class DoctorScheduleController extends Controller
{
    public function index()
    {
        $doctor = Doctor::where('user_id', auth()->id())->firstOrFail();
        $schedule = json_decode($doctor->working_hours, true);
        
        return view('doctor.schedule.index', compact('schedule'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'working_hours' => ['required', 'array'],
            'working_hours.*' => ['array'],
            'working_hours.*.*' => ['string', 'regex:/^\d{2}:\d{2}-\d{2}:\d{2}$/']
        ]);

        $doctor = Doctor::where('user_id', auth()->id())->firstOrFail();
        $doctor->working_hours = json_encode($validated['working_hours']);
        $doctor->save();

        return redirect()->route('doctor.schedule.index')
            ->with('success', 'Schedule updated successfully');
    }
} 