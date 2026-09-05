<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class ProductionConfigurationTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_does_not_create_an_admin_without_explicit_credentials(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('users', 0);
    }

    public function test_database_seeder_creates_the_configured_admin_without_resetting_existing_passwords(): void
    {
        config([
            'app.env' => 'testing',
        ]);
        config([
            'admin.name' => 'Configured Admin',
            'admin.email' => 'admin@example.test',
            'admin.password' => 'initial-password',
        ]);

        $this->seed(DatabaseSeeder::class);

        $admin = User::where('email', 'admin@example.test')->firstOrFail();

        $this->assertSame('Configured Admin', $admin->name);
        $this->assertTrue($admin->is_superuser);

        $admin->update(['password' => 'changed-password']);

        $this->seed(DatabaseSeeder::class);

        $this->assertTrue(password_verify('changed-password', (string) $admin->fresh()->password));
    }

    public function test_admin_login_uses_https_for_the_brand_logo_url(): void
    {
        URL::forceRootUrl('https://trenou.onrender.com');

        $this->assertSame(
            'https://trenou.onrender.com/images/logo/alu-la-solution-full.webp',
            secure_asset('images/logo/alu-la-solution-full.webp'),
        );
    }
}
