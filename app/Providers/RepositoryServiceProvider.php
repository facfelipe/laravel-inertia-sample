<?php

namespace App\Providers;

use App\Repositories\MedicalRecordRepository;
use App\Repositories\MedicalRecordRepositoryInterface;
use App\Repositories\PatientRepository;
use App\Repositories\PatientRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(PatientRepositoryInterface::class, PatientRepository::class);
        $this->app->bind(MedicalRecordRepositoryInterface::class, MedicalRecordRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
