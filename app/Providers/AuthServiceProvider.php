<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\MedicalDocument;
use App\Policies\MedicalDocumentPolicy;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('delete-document', function (User $user, MedicalDocument $document) {
            return $user->id === $document->user_id || $user->role === 'admin';
        });

        Gate::define('view-document', function (User $user, MedicalDocument $document) {
            return $user->id === $document->user_id || $user->role === 'admin';
        });

        Gate::define('access-doctor-dashboard', function ($user) {
            return $user->role === 'doctor';
        });
    }

    /**
     * Register policies.
     *
     * @return void
     */
    protected function registerPolicies()
    {
        $this->policies = [
            MedicalDocument::class => MedicalDocumentPolicy::class,
        ];
    }
} 