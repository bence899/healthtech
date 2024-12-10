<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;

class StoreDocumentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $maxFileSize = config('documents.max_file_size', 10240); // 10MB default
        $maxMonthlyUploads = config('documents.monthly_upload_limit', 50);
        
        return [
            'document' => [
                'required',
                'file',
                'max:' . $maxFileSize,
                function ($attribute, $value, $fail) use ($maxMonthlyUploads) {
                    // Check monthly upload limit
                    $monthlyCount = auth()->user()->medicalDocuments()
                        ->whereMonth('created_at', now()->month)
                        ->count();
                    
                    if ($monthlyCount >= $maxMonthlyUploads) {
                        $fail('Monthly upload limit exceeded.');
                    }

                    // Check total storage limit
                    $totalStorage = auth()->user()->medicalDocuments()->sum('file_size');
                    $maxStorage = config('documents.max_storage', 104857600); // 100MB default
                    
                    if (($totalStorage + $value->getSize()) > $maxStorage) {
                        $fail('Total storage limit exceeded.');
                    }
                }
            ],
            'title' => 'required|string|max:255',
            'description' => 'nullable|string'
        ];
    }
} 