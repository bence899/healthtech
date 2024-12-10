<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Patient;

class DoctorDashboardController extends Controller
{
    public function index()
    {
        $doctorId = auth()->user()->doctor->id;
        
        $todayAppointments = Appointment::with('patient.user')
            ->where('doctor_id', $doctorId)
            ->whereDate('appointment_date', today())
            ->orderBy('appointment_date')
            ->get();

        $pendingAppointments = Appointment::with('patient.user')
            ->where('doctor_id', $doctorId)
            ->where('status', 'pending')
            ->orderBy('appointment_date')
            ->get();

        try {
            // Get unique patients with their appointment details
            $uniquePatients = Patient::with(['user', 'appointments'])
                ->whereHas('appointments', function ($query) use ($doctorId) {
                    $query->where('doctor_id', $doctorId);
                })
                ->withCount(['appointments' => function ($query) use ($doctorId) {
                    $query->where('doctor_id', $doctorId);
                }])
                ->withMax('appointments as appointments_max_appointment_date', 'appointment_date')
                ->get();

            $totalPatients = $uniquePatients->count();
        } catch (\Exception $e) {
            $uniquePatients = collect([]);
            $totalPatients = 0;
        }

        return view('doctor.dashboard', compact(
            'todayAppointments',
            'pendingAppointments',
            'totalPatients',
            'uniquePatients'
        ));
    }

    public function respondToAppointment(Request $request, Appointment $appointment)
    {
        if ($appointment->doctor_id !== auth()->user()->doctor->id) {
            abort(403);
        }

        if ($request->input('action') === 'confirm') {
            // Check for time conflicts
            $hasConflict = Appointment::where('doctor_id', auth()->user()->doctor->id)
                ->where('id', '!=', $appointment->id)
                ->where('status', 'confirmed')
                ->whereDate('appointment_date', $appointment->appointment_date)
                ->whereTime('appointment_date', $appointment->appointment_date->format('H:i:s'))
                ->exists();

            if ($hasConflict) {
                return back()->with('error', 'Cannot confirm appointment due to schedule conflict.');
            }

            $appointment->update(['status' => 'confirmed']);
            return back()->with('success', 'Appointment confirmed successfully.');
        } else {
            $request->validate([
                'cancellation_reason' => 'required|string|max:255'
            ]);

            $appointment->update([
                'status' => 'cancelled',
                'cancellation_reason' => $request->cancellation_reason
            ]);
            return back()->with('success', 'Appointment declined with reason.');
        }
    }

    public function appointments()
    {
        $doctorId = auth()->user()->doctor->id;
        
        $appointments = Appointment::with(['patient.user'])
            ->where('doctor_id', $doctorId)
            ->orderBy('appointment_date', 'desc')
            ->get();

        return view('doctor.appointments.index', compact('appointments'));
    }
} 