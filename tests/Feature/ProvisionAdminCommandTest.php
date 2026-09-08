<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProvisionAdminCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_provisions_the_configured_admin_without_resetting_an_existing_password(): void
    {
        config([
            'admin.name' => 'Administrateur TRENOU',
            'admin.email' => 'agbokpeablamvi@gmail.com',
            'admin.password' => 'initial-password',
            'admin.force_password_reset' => false,
        ]);

        $this->artisan('app:provision-admin')->assertSuccessful();

        $admin = User::where('email', 'agbokpeablamvi@gmail.com')->firstOrFail();
        $admin->update(['password' => 'changed-password']);

        $this->artisan('app:provision-admin')->assertSuccessful();

        $this->assertTrue(password_verify('changed-password', (string) $admin->fresh()->password));
    }

    public function test_it_can_reset_the_configured_admin_password_when_explicitly_enabled(): void
    {
        config([
            'admin.email' => 'agbokpeablamvi@gmail.com',
            'admin.password' => 'reset-password',
            'admin.force_password_reset' => true,
        ]);

        User::factory()->create([
            'email' => 'agbokpeablamvi@gmail.com',
            'password' => 'old-password',
        ]);

        $this->artisan('app:provision-admin')->assertSuccessful();

        $this->assertTrue(password_verify('reset-password', (string) User::first()->password));
    }
}
