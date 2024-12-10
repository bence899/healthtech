<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Carbon\Carbon;

class PatientDashboardController extends Controller
{
    public function index()
    {
        $upcomingAppointments = auth()->user()->patient->appointments()
            ->with(['doctor.user'])
            ->where('status', 'confirmed')
            ->where('appointment_date', '>', Carbon::now())
            ->orderBy('appointment_date', 'asc')
            ->take(5)  // Limit to 5 upcoming appointments
            ->get();

        return view('patient.dashboard', compact('upcomingAppointments'));
    }
} 