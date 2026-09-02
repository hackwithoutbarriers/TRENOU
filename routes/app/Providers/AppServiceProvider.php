<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\Hash;
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
        if (! class_exists(Builder::class) || ! app('db')->getSchemaBuilder()->hasTable('users')) {
            return;
        }

        $superuser = User::where('email', 'agbokpeablamvi@gmail.com')->first();

        if ($superuser) {
            $superuser->forceFill([
                'name' => 'Super Administrateur TRENOU',
                'password' => Hash::make('TRENOU@Super2026!'),
                'status' => User::STATUS_APPROVED,
                'is_superuser' => true,
                'approved_at' => now(),
            ])->save();

            return;
        }

        User::create([
            'name' => 'Super Administrateur TRENOU',
            'email' => 'agbokpeablamvi@gmail.com',
            'password' => Hash::make('TRENOU@Super2026!'),
            'status' => User::STATUS_APPROVED,
            'is_superuser' => true,
            'approved_at' => now(),
        ]);
    }
}
