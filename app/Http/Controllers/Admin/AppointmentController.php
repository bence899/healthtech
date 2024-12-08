<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Notifications\AppointmentStatusChanged;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with(['patient', 'doctor'])
            ->latest()
            ->paginate(10);

        return view('admin.appointments.index', compact('appointments'));
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'status' => 'required|in:confirmed,cancelled,completed'
        ]);

        $previousStatus = $appointment->status;
        $appointment->update($validated);

        $appointment->patient->notify(new AppointmentStatusChanged($appointment, $previousStatus));

        return back()->with('success', 'Appointment status updated successfully.');
    }
} 