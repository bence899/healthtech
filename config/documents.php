<?php

return [
    // Maximum file size in kilobytes (10MB)
    'max_file_size' => env('MAX_DOCUMENT_SIZE', 10240),

    // Maximum monthly uploads per user
    'monthly_upload_limit' => env('MONTHLY_UPLOAD_LIMIT', 50),

    // Maximum total storage per user in bytes (100MB)
    'max_storage' => env('MAX_STORAGE_LIMIT', 104857600),

    // Allowed file types
    'allowed_types' => [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ]
]; 