<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'title',
        'file_path',
        // Add other relevant fields
    ];

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }
} 