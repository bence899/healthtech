<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'specialization',
        'qualifications',
        'experience',
        'is_available',
        'working_hours',
        'consultation_fee',
        'bio'
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'working_hours' => 'array'
    ];

    /**
     * Get the user that owns the doctor profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the doctor's full name through the user relationship.
     */
    public function getFullNameAttribute(): string
    {
        return 'Dr. ' . $this->user->name;
    }

    /**
     * Scope a query to only include available doctors.
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function getScheduleAttribute()
    {
        return $this->working_hours ?? [
            'monday' => ['09:00-17:00'],
            'tuesday' => ['09:00-17:00'],
            'wednesday' => ['09:00-17:00'],
            'thursday' => ['09:00-17:00'],
            'friday' => ['09:00-17:00']
        ];
    }
}
