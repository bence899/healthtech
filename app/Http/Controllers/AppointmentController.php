<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Notifications\AppointmentStatusChanged;
use App\Models\Patient;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $patient = auth()->user()->patient;
        
        if (!$patient) {
            $patient = Patient::create(['user_id' => auth()->id()]);
        }
        
        $appointments = $patient->appointments()
            ->with(['doctor.user'])
            ->orderBy('appointment_date', 'asc')
            ->get();

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
        \Log::info('Appointment request data:', $request->all());
        
        try {
            $validated = $request->validate([
                'doctor_id' => 'required|exists:doctors,id',
                'appointment_date' => 'required|date',
                'appointment_time' => 'required',
                'reason_for_visit' => 'required|string|max:1000'
            ]);

            // Combine date and time
            $appointmentDateTime = Carbon::parse($request->appointment_date . ' ' . $request->appointment_time);
            
            // Check if the appointment time is in the future
            if ($appointmentDateTime->isPast()) {
                return back()
                    ->withInput()
                    ->withErrors(['appointment_date' => 'Appointment date must be in the future']);
            }

            // Check if doctor is available at this time
            $doctor = Doctor::findOrFail($request->doctor_id);
            $dayOfWeek = strtolower($appointmentDateTime->format('l'));
            
            // Check for existing appointments
            $hasConflict = Appointment::where('doctor_id', $request->doctor_id)
                ->where('status', '!=', 'cancelled')
                ->whereDate('appointment_date', $appointmentDateTime->toDateString())
                ->whereTime('appointment_date', $appointmentDateTime->format('H:i:s'))
                ->exists();

            if ($hasConflict) {
                return back()
                    ->withInput()
                    ->withErrors(['appointment_time' => 'This time slot is already booked']);
            }

            // Create the appointment
            $appointment = Appointment::create([
                'doctor_id' => $validated['doctor_id'],
                'patient_id' => auth()->user()->patient->id,
                'appointment_date' => $appointmentDateTime,
                'reason_for_visit' => $validated['reason_for_visit'],
                'status' => 'pending'
            ]);

            return redirect()->route('appointments.index')
                ->with('success', 'Appointment booked successfully.');
            
        } catch (\Exception $e) {
            \Log::error('Appointment creation failed: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to book appointment. Please try again.']);
        }
    }

    public function cancel(Appointment $appointment)
    {
        if ($appointment->patient_id !== auth()->user()->patient->id) {
            abort(403);
        }

        if ($appointment->status === 'completed') {
            return redirect()->back()
                ->with('error', 'Cannot cancel a completed appointment');
        }

        $appointment->update(['status' => 'cancelled']);
        auth()->user()->notify(new AppointmentStatusChanged($appointment));

        return redirect()->back()
            ->with('success', 'Appointment cancelled successfully');
    }
}
