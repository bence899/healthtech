<?php

namespace App\Observers;

use App\Models\Appointment;
use App\Models\Patient;

class AppointmentObserver
{
    public function creating(Appointment $appointment)
    {
        // Only try to create patient if we don't have a patient_id set
        if (!$appointment->patient_id && auth()->check()) {
            $patient = Patient::firstOrCreate(['user_id' => auth()->id()]);
            $appointment->patient_id = $patient->id;
        }
    }
} 