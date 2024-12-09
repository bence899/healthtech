<?php

namespace App\Policies;

use App\Models\MedicalDocument;
use App\Models\User;

class MedicalDocumentPolicy
{
    public function view(User $user, MedicalDocument $document): bool
    {
        return $user->id === $document->user_id || $user->role === 'admin';
    }

    public function delete(User $user, MedicalDocument $document): bool
    {
        return $user->id === $document->user_id || $user->role === 'admin';
    }
} 