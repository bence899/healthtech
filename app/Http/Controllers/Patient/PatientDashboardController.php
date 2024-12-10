<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\MedicalDocument;
use App\Models\Patient;
use Illuminate\Http\Request;

class PatientDashboardController extends Controller
{
    public function index()
    {
        $patient = Patient::firstOrCreate(
            ['user_id' => auth()->id()],
            ['created_at' => now()]
        );

        $upcoming_appointments = Appointment::with('doctor.user')
            ->where('patient_id', $patient->id)
            ->where('appointment_date', '>=', now())
            ->orderBy('appointment_date')
            ->take(5)
            ->get();

        $recent_documents = MedicalDocument::where('user_id', auth()->id())
            ->latest()
            ->take(3)
            ->get();

        return view('patient.dashboard', compact('upcoming_appointments', 'recent_documents'));
    }
} 