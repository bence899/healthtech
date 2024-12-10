<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;

class DoctorDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'today_appointments' => Appointment::where('doctor_id', auth()->user()->doctor->id)
                ->whereDate('appointment_date', today())
                ->count(),
            'pending_appointments' => Appointment::where('doctor_id', auth()->user()->doctor->id)
                ->where('status', 'pending')
                ->count(),
            'total_patients' => Appointment::where('doctor_id', auth()->user()->doctor->id)
                ->distinct('patient_id')
                ->count('patient_id'),
        ];

        $upcoming_appointments = Appointment::with('patient')
            ->where('doctor_id', auth()->user()->doctor->id)
            ->where('appointment_date', '>=', now())
            ->orderBy('appointment_date')
            ->take(5)
            ->get();

        return view('doctor.dashboard', compact('stats', 'upcoming_appointments'));
    }
} 