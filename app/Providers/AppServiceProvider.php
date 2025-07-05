<?php

namespace App\Providers;

use App\Modules\Supplier\Repositories\Contracts\SupplierRepository as SupplierRepositoryContract;
use App\Modules\Supplier\Repositories\SupplierRepository;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            SupplierRepositoryContract::class,
            SupplierRepository::class
        );
    }

    public function boot(): void
    {
        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }
}
