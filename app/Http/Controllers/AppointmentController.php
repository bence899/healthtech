<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Notifications\AppointmentStatusChanged;
use App\Models\Patient;

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
        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required|date_format:H:i',
            'reason_for_visit' => 'required|string|max:500'
        ]);

        try {
            $appointmentDateTime = \Carbon\Carbon::parse(
                $validated['appointment_date'] . ' ' . $validated['appointment_time']
            )->format('Y-m-d H:i:s');

            $patient = auth()->user()->patient ?? Patient::create(['user_id' => auth()->id()]);

            $appointment = Appointment::create([
                'patient_id' => $patient->id,
                'doctor_id' => $validated['doctor_id'],
                'appointment_date' => $appointmentDateTime,
                'reason_for_visit' => $validated['reason_for_visit'],
                'status' => 'pending'
            ]);

            return redirect()->route('appointments.index')
                ->with('success', 'Appointment booked successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->withErrors(['appointment_date' => 'Invalid date/time format']);
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
