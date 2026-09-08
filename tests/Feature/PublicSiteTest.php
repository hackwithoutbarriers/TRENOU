<?php

namespace Tests\Feature;

use App\Models\Devis;
use App\Models\Projet;
use App\Models\PublicDevis;
use App\Models\Temoignage;
use App\Models\User;
use Database\Seeders\ProjetSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
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
        $response->assertSee('aria-controls="mobile-menu"', false);
        $response->assertSee('images/hero/atelier-aluminium-1.svg', false);
        $response->assertDontSee('images.unsplash.com');
        $response->assertSee('alt=""', false);
        $response->assertDontSee('>Avis<', false);
    }

    public function test_public_seo_endpoints_and_metadata_are_available(): void
    {
        $this->get(route('robots'))
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('Sitemap: '.route('sitemap'), false)
            ->assertSee('Disallow: /admin', false);

        $this->get(route('sitemap'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee('<loc>'.route('home').'</loc>', false)
            ->assertSee('<loc>'.route('services.detail', ['slug' => 'menuiserie-batiment']).'</loc>', false);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<meta property="og:image"', false)
            ->assertSee('<link rel="canonical"', false)
            ->assertSee('application/ld+json', false);
    }

    public function test_reviews_navigation_and_home_section_are_conditional_on_published_reviews(): void
    {
        Temoignage::create([
            'nom_client' => 'Afi K.',
            'ville' => 'Lomé',
            'projet_type' => 'batiment',
            'projet_ref' => 'DEV-00002',
            'note' => 5,
            'texte' => 'Une réalisation soignée et un suivi très clair.',
            'date_projet' => '2026-03-14',
            'consentement' => true,
            'statut' => 'publie',
            'source' => 'interne',
        ]);

        $response = $this->get(route('home'));

        $response->assertSee('Avis');
        $response->assertSee('Ce que disent nos clients');
    }

    public function test_quote_page_exposes_mobile_dimension_and_submission_controls(): void
    {
        $response = $this->get(route('public.devis'));

        $response->assertOk();
        $response->assertSee('id="quote-validation-message"', false);
        $response->assertSee('id="submit-quote" class="hidden ', false);
        $response->assertDontSee('id="submit-quote" class="hidden inline-flex', false);
    }

    public function test_project_seeder_provides_editable_visible_demo_projects(): void
    {
        $this->seed(ProjetSeeder::class);

        $this->assertGreaterThanOrEqual(6, Projet::query()->where('is_visible_public', true)->count());
        $this->assertNotEmpty(Projet::query()->where('is_visible_public', true)->first()->images);
    }

    public function test_project_seeder_does_not_overwrite_admin_edits(): void
    {
        $this->seed(ProjetSeeder::class);
        $project = Projet::query()->firstOrFail();
        $project->update(['titre' => 'Projet édité depuis l’administration']);

        $this->seed(ProjetSeeder::class);

        $this->assertDatabaseHas('projets', [
            'id' => $project->id,
            'titre' => 'Projet édité depuis l’administration',
        ]);
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

    public function test_gallery_resolves_stored_project_images(): void
    {
        Config::set('filesystems.default', 'public');
        Storage::fake('public');
        Projet::create([
            'titre' => 'Villa illustrée',
            'categorie' => 'batiment',
            'description' => 'Projet avec image stockée.',
            'ville' => 'Lomé',
            'pays' => 'Togo',
            'is_visible_public' => true,
            'images' => ['projets/villa.jpg'],
        ]);

        $response = $this->get(route('gallery'));

        $response->assertOk();
        $response->assertSee(Storage::disk('public')->url('projets/villa.jpg'), false);
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

    public function test_contact_form_is_saved(): void
    {
        $this->withSession(['_token' => 'test-token']);

        $response = $this->post(route('contact.store'), [
            '_token' => 'test-token',
            'nom' => 'Adjoa Koffi',
            'email' => 'adjoa@example.com',
            'telephone' => '+228 90 00 00 00',
            'sujet' => 'Projet de baie vitrée',
            'message' => 'Je souhaite recevoir des informations sur vos réalisations.',
        ]);

        $response->assertRedirect(route('contact'));
        $this->assertDatabaseHas('contact_messages', [
            'nom' => 'Adjoa Koffi',
            'email' => 'adjoa@example.com',
            'sujet' => 'Projet de baie vitrée',
        ]);
    }

    public function test_contact_form_notifies_configured_whatsapp_webhook(): void
    {
        Config::set('services.whatsapp.webhook_url', 'https://hooks.example.test/contact');
        Config::set('services.whatsapp.webhook_secret', 'test-secret');
        Http::fake();

        $this->withSession(['_token' => 'test-token'])->post(route('contact.store'), [
            '_token' => 'test-token',
            'nom' => 'Adjoa Koffi',
            'email' => 'adjoa@example.com',
            'telephone' => '+228 90 00 00 00',
            'sujet' => 'Projet de baie vitrée',
            'message' => 'Je souhaite recevoir des informations sur vos réalisations.',
        ])->assertRedirect(route('contact'));

        Http::assertSent(function ($request): bool {
            return $request->url() === 'https://hooks.example.test/contact'
                && $request->header('X-Webhook-Secret')[0] === 'test-secret'
                && data_get($request->data(), 'message.subject') === 'Projet de baie vitrée';
        });
    }

    public function test_contact_form_succeeds_when_whatsapp_webhook_is_unavailable(): void
    {
        Config::set('services.whatsapp.webhook_url', 'https://hooks.example.test/contact');
        Http::fake(function (): never {
            throw new ConnectionException('Webhook indisponible');
        });

        $this->withSession(['_token' => 'test-token'])
            ->post(route('contact.store'), [
                '_token' => 'test-token',
                'nom' => 'Adjoa Koffi',
                'email' => 'adjoa@example.com',
                'telephone' => '+228 90 00 00 00',
                'sujet' => 'Projet de baie vitrée',
                'message' => 'Je souhaite recevoir des informations sur vos réalisations.',
            ])
            ->assertRedirect(route('contact'));

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'adjoa@example.com',
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
        $response->assertSee('Adresse e-mail');
        $response->assertSee('Mot de passe');
        $response->assertSee('Se connecter');
        $response->assertSee('Créer un compte artisan');
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

    public function test_quote_reference_is_five_digits_and_is_not_reused_after_deletion(): void
    {
        $devis = Devis::create([
            'client_nom' => 'Komi Adje',
            'client_telephone' => '+228 96 00 00 00',
            'client_pays' => 'Togo',
            'description_chantier' => 'Projet test',
            'lignes_facturation' => [],
            'montant_main_doeuvre' => 0,
            'statut' => 'brouillon',
        ]);

        $reference = $devis->reference_publique;

        $this->assertMatchesRegularExpression('/^[0-9]{5}$/', $reference);
        $this->assertSame('DEV-'.$reference, $devis->numero_devis);
        $this->assertSame($reference, $devis->getRouteKey());

        $devis->delete();

        $this->assertDatabaseHas('devis_public_references', [
            'reference_publique' => $reference,
            'devis_id' => $devis->id,
        ]);

        $replacement = Devis::create([
            'client_nom' => 'Ama Mensah',
            'client_telephone' => '+228 90 00 00 00',
            'client_pays' => 'Togo',
            'description_chantier' => 'Projet test 2',
            'lignes_facturation' => [],
            'montant_main_doeuvre' => 0,
            'statut' => 'brouillon',
        ]);

        $this->assertNotSame($reference, $replacement->reference_publique);
    }

    public function test_quote_saving_ignores_malformed_billing_lines(): void
    {
        $devis = Devis::create([
            'client_nom' => 'Komi Adje',
            'client_telephone' => '+228 96 00 00 00',
            'client_pays' => 'Togo',
            'description_chantier' => 'Projet test',
            'lignes_facturation' => [
                null,
                'ligne invalide',
                ['designation' => 'Porte', 'quantite' => 2, 'prix_unitaire' => 5000],
            ],
            'montant_main_doeuvre' => 1000,
            'statut' => 'brouillon',
        ]);

        $this->assertSame('10000.00', $devis->montant_materiel);
        $this->assertSame('11000.00', $devis->montant_total);
        $this->assertCount(1, $devis->billingLines());
        $this->assertDatabaseHas('devis_public_references', ['devis_id' => $devis->id]);
        $this->assertSame(1, DB::table('devis_public_references')->where('devis_id', $devis->id)->count());
    }

    public function test_reviews_page_shows_verified_internal_reviews(): void
    {
        Temoignage::create([
            'nom_client' => 'Kokou A.',
            'ville' => 'Kara',
            'projet_type' => 'mobilier',
            'projet_ref' => 'DEV-00001',
            'note' => 5,
            'texte' => 'Travail propre, finition impeccable et bonne communication.',
            'photo_projet' => '/uploads/temoignages/tm_042.jpg',
            'date_projet' => '2026-03-14',
            'consentement' => true,
            'statut' => 'publie',
            'source' => 'interne',
        ]);

        $response = $this->get(route('reviews'));

        $response->assertOk();
        $response->assertSee('Kokou A.');
        $response->assertSee('Client vérifié');
    }

    public function test_google_reviews_sync_command_runs_without_google_configuration(): void
    {
        $this->artisan('app:sync-google-reviews')->assertSuccessful();
    }

    public function test_customer_can_submit_a_verified_review_with_photo(): void
    {
        Storage::fake('public');

        $response = $this->post(route('reviews.store'), [
            '_token' => 'test-token',
            'nom_client' => 'Kokou A.',
            'ville' => 'Kara',
            'projet_type' => 'mobilier',
            'projet_ref' => 'DEV-00001',
            'note' => 5,
            'texte' => 'Très bon travail, qualité irréprochable et communication claire de bout en bout.',
            'date_projet' => '2026-03-14',
            'photo_projet' => UploadedFile::fake()->image('proof.jpg', 1200, 900),
            'consentement' => '1',
        ]);

        $response->assertRedirect(route('reviews'));
        $this->assertDatabaseHas('temoignages', [
            'nom_client' => 'Kokou A.',
            'note' => 5,
            'consentement' => true,
            'statut' => 'en_attente',
            'source' => 'interne',
        ]);
    }
}
