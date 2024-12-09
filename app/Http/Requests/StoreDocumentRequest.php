<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Controllers\DocumentController;

class StoreDocumentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'document' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:' . DocumentController::MAX_FILE_SIZE,
            ]
        ];
    }

    public function messages()
    {
        return [
            'document.max' => 'The document field must not be greater than ' . DocumentController::MAX_FILE_SIZE . ' kilobytes.',
            'document.mimes' => 'The document must be a PDF, JPG, or PNG file.'
        ];
    }
} 