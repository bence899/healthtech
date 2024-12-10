<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'file_path',
        'file_type',
        'file_size',
        'upload_month'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 