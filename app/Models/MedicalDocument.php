<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'file_path',
        'file_type',
        'file_size',
        'user_id',
        'upload_month'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($document) {
            if (!$document->upload_month) {
                $document->upload_month = now()->format('Y-m');
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 