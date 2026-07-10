<?php

namespace App\Providers;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->environment('local')) {
            try {
                if (User::count() === 0) {
                    $this->app->make(DatabaseSeeder::class)->run();
                }
            } catch (\Exception $e) {
                //
            }
        }
    }
}
