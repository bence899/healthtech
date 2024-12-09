<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Http\Request;
use App\Notifications\AppointmentStatusChanged;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::with(['patient', 'doctor.user']);

        // Search functionality
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('patient', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('doctor.user', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            });
        }

        // Status filter
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Date filter
        if ($request->date) {
            $query->whereDate('appointment_date', $request->date);
        }

        // Doctor filter
        if ($request->doctor_id) {
            $query->where('doctor_id', $request->doctor_id);
        }

        $doctors = Doctor::with('user')->get();
        $appointments = $query->latest()->paginate(10)->withQueryString();

        return view('admin.appointments.index', compact('appointments', 'doctors'));
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'status' => 'required|in:confirmed,cancelled,completed,pending'
        ]);

        $previousStatus = $appointment->status;
        $appointment->update($validated);

        $appointment->patient->notify(new AppointmentStatusChanged($appointment, $previousStatus));

        return back()->with('success', 'Appointment status updated successfully.');
    }
} 