<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $appointments = auth()->user()->appointments;
        return view('appointments.index', compact('appointments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $doctors = Doctor::with('user')->where('is_available', true)->get();
        return view('appointments.create', compact('doctors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date|after:today',
            'reason_for_visit' => 'required|string|max:255',
        ]);

        $appointment = Appointment::create([
            'patient_id' => auth()->id(),
            'doctor_id' => $validated['doctor_id'],
            'appointment_date' => $validated['appointment_date'],
            'reason_for_visit' => $validated['reason_for_visit'],
            'status' => 'pending'
        ]);

        return redirect()->route('appointments.index')
        ->with('success', 'Appointment scheduled successfully!');
    }
}
