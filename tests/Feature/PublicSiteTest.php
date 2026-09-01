<?php

namespace Tests\Feature;

use App\Models\Projet;
use App\Models\PublicDevis;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_is_accessible(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('TRENOU');
    }

    public function test_gallery_shows_only_public_projects_with_filters(): void
    {
        Projet::create([
            'titre' => 'Villa sahélienne',
            'categorie' => 'batiment',
            'description' => 'Projet de rénovation',
            'ville' => 'Lomé',
            'pays' => 'Togo',
            'is_visible_public' => true,
            'images' => ['projets/preview.jpg'],
        ]);

        Projet::create([
            'titre' => 'Projet masqué',
            'categorie' => 'mobilier',
            'description' => 'Masqué du public',
            'ville' => 'Cotonou',
            'pays' => 'Bénin',
            'is_visible_public' => false,
            'images' => ['projets/hidden.jpg'],
        ]);

        $response = $this->get(route('gallery', ['categorie' => 'batiment', 'ville' => 'Lomé', 'pays' => 'Togo']));

        $response->assertOk();
        $response->assertSee('Villa sahélienne');
        $response->assertDontSee('Projet masqué');
    }

    public function test_public_quote_form_is_saved(): void
    {
        $this->withSession(['_token' => 'test-token']);

        $response = $this->post(route('public.devis.store'), [
            '_token' => 'test-token',
            'nom' => 'Adjoa Koffi',
            'telephone' => '+228 90 00 00 00',
            'ville' => 'Lomé',
            'pays' => 'Togo',
            'description_besoin' => 'Je souhaite refaire la cuisine de ma maison.',
        ]);

        $response->assertRedirect(route('public.devis'));
        $this->assertDatabaseHas('public_devis', [
            'nom' => 'Adjoa Koffi',
            'telephone' => '+228 90 00 00 00',
            'pays' => 'Togo',
        ]);
    }

    public function test_home_page_keeps_artisan_access_discreet_in_footer(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertDontSee('Espace artisan');
        $response->assertSee('Accès pro');
        $response->assertSee('/admin');
    }

    public function test_admin_login_page_is_accessible_for_guests(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');

        $response = $this->get('/admin/login');

        $response->assertOk();
        $response->assertSee('Accédez à votre espace artisan');
    }

    public function test_registration_page_is_accessible(): void
    {
        $response = $this->get('/admin/register');

        $response->assertOk();
        $response->assertSee('Créer votre accès sécurisé');
        $response->assertSee('Protection renforcée');
    }

    public function test_two_factor_verification_works_for_confirmed_users(): void
    {
        $secret = app(Google2FA::class)->generateSecretKey();
        $user = User::create([
            'name' => 'Artisan TRENOU',
            'email' => 'artisan@trenou.tg',
            'password' => bcrypt('secret123'),
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ]);

        $this->assertTrue($user->verifyTwoFactorCode(app(Google2FA::class)->getCurrentOtp($secret)));
    }

    public function test_public_quote_can_be_converted_into_official_quote(): void
    {
        $publicDevis = PublicDevis::create([
            'nom' => 'Komi Adje',
            'telephone' => '+228 96 00 00 00',
            'ville' => 'Atakpamé',
            'pays' => 'Togo',
            'description_besoin' => 'Je veux refaire la clôture et l’aménagement intérieur.',
            'statut' => 'nouvelle',
        ]);

        $devis = $publicDevis->convertToDevis();

        $this->assertDatabaseHas('devis', [
            'client_nom' => 'Komi Adje',
            'client_telephone' => '+228 96 00 00 00',
            'description_chantier' => 'Je veux refaire la clôture et l’aménagement intérieur.',
        ]);
        $this->assertSame('convertie', $publicDevis->fresh()->statut);
        $this->assertNotNull($devis->numero_devis);
    }
}
