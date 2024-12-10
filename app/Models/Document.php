<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'path',
        'file_size',
        'original_name',
        'mime_type',
        'user_id'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($document) {
            if (!$document->file_size && $document->path) {
                $document->file_size = Storage::size($document->path);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStorageUsed()
    {
        return $this->file_size;
    }

    public function isWithinLimits()
    {
        $maxStorage = config('documents.max_storage');
        $userStorage = $this->user->documents()->sum('file_size');
        
        return ($userStorage + $this->file_size) <= $maxStorage;
    }
} 