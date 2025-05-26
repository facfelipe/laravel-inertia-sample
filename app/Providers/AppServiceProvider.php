<?php

namespace App\Providers;

use App\Models\MedicalRecord;
use App\Observers\MedicalRecordObserver;
use App\Policies\MedicalRecordPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register model observers
        MedicalRecord::observe(MedicalRecordObserver::class);

        // Register policies
        Gate::policy(MedicalRecord::class, MedicalRecordPolicy::class);
    }
}
