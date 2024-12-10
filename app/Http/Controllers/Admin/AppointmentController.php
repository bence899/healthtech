<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Http\Request;
use App\Notifications\AppointmentStatusChanged;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with(['patient', 'doctor.user'])->paginate(10);
        $doctors = Doctor::with('user')->get();
        
        return view('admin.appointments.index', compact('appointments', 'doctors'));
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:confirmed,cancelled,completed,pending']
        ]);

        $appointment->update($validated);
        
        $appointment->patient->user->notify(new AppointmentStatusChanged($appointment));

        return redirect()->back()->with('success', 'Appointment status updated successfully.');
    }

    public function cancel(Appointment $appointment)
    {
        if ($appointment->patient_id !== auth()->user()->patient->id) {
            abort(403);
        }

        if (in_array($appointment->status, ['completed', 'confirmed'])) {
            return redirect()->back()
                ->with('error', 'Cannot cancel a confirmed or completed appointment');
        }

        $appointment->update(['status' => 'cancelled']);
        
        return redirect()->back()
            ->with('success', 'Appointment cancelled successfully');
    }
} 