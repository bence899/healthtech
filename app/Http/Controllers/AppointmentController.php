<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Notifications\AppointmentStatusChanged;

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
        $doctors = Doctor::with('user')
            ->where('is_available', true)
            ->get();
        
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

        // Get doctor's schedule
        $doctor = Doctor::findOrFail($validated['doctor_id']);
        $schedule = json_decode($doctor->working_hours, true) ?? $doctor->schedule;
        
        // Get day of week from appointment date
        $dayOfWeek = strtolower(date('l', strtotime($validated['appointment_date'])));
        
        // Get appointment time
        $appointmentTime = date('H:i', strtotime($validated['appointment_date']));
        
        // Check if day exists in schedule
        if (!isset($schedule[$dayOfWeek])) {
            return back()
                ->withInput()
                ->withErrors(['appointment_date' => 'Doctor is not available on this day.']);
        }

        // Check if time falls within any of the doctor's time slots
        $isWithinSchedule = false;
        foreach ($schedule[$dayOfWeek] as $timeSlot) {
            [$start, $end] = explode('-', $timeSlot);
            if ($appointmentTime >= $start && $appointmentTime <= $end) {
                $isWithinSchedule = true;
                break;
            }
        }

        if (!$isWithinSchedule) {
            return back()
                ->withInput()
                ->withErrors(['appointment_date' => 'Selected time is outside doctor\'s working hours.']);
        }

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

    public function cancel(Appointment $appointment)
    {
        // Check if the user owns this appointment
        if ($appointment->patient_id !== auth()->id()) {
            abort(403);
        }

        // Only allow cancellation of pending or confirmed appointments
        if (!in_array($appointment->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'This appointment cannot be cancelled.');
        }

        $previousStatus = $appointment->status;

        $appointment->update(['status' => 'cancelled']);

        $appointment->patient->notify(new AppointmentStatusChanged($appointment, $previousStatus));

        return back()->with('success', 'Appointment cancelled successfully.');
    }
}
