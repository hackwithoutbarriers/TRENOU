<?php

namespace Tests\Feature;

use App\Models\Attestation;
use App\Models\Devis;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PdfDocumentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create([
            'status' => User::STATUS_APPROVED,
            'is_superuser' => true,
        ]));
    }

    public function test_pdf_routes_require_an_approved_user(): void
    {
        $attestation = Attestation::create([
            'apprenti_nom_prenom' => 'Test',
            'date_debut_apprentissage' => '2024-01-01',
            'date_fin_apprentissage' => '2025-01-01',
            'date_delivrance' => '2025-01-02',
        ]);
        auth()->logout();

        $this->get(route('attestation.pdf', $attestation))->assertForbidden();

        $pendingUser = User::factory()->create(['status' => User::STATUS_PENDING]);
        $this->actingAs($pendingUser);

        $this->get(route('attestation.pdf', $attestation))->assertForbidden();
    }

    public function test_certificate_and_attestation_numbers_share_the_same_immutable_serial(): void
    {
        $attestation = Attestation::create([
            'apprenti_nom_prenom' => 'Kokou Adje',
            'date_debut_apprentissage' => '2024-01-15',
            'date_fin_apprentissage' => '2026-01-15',
            'date_delivrance' => '2026-09-03',
            'type_document' => 'certificat',
        ]);

        $this->assertSame('CERT-2659', $attestation->documentNumber('CERT'));
        $this->assertSame('ATT-2659', $attestation->documentNumber('ATT'));
        $this->assertSame('2659', $attestation->serialNumber());

        $attestation->delete();

        $replacement = Attestation::create([
            'apprenti_nom_prenom' => 'Ama Mensah',
            'date_debut_apprentissage' => '2024-01-15',
            'date_fin_apprentissage' => '2026-01-15',
            'date_delivrance' => '2026-09-03',
        ]);

        $this->assertSame('2660', $replacement->serialNumber());
    }

    public function test_document_links_page_exposes_both_pdf_downloads(): void
    {
        $attestation = Attestation::create([
            'apprenti_nom_prenom' => 'Kokou Adje',
            'date_debut_apprentissage' => '2024-01-15',
            'date_fin_apprentissage' => '2026-01-15',
            'date_delivrance' => '2026-09-03',
            'type_document' => 'certificat',
        ]);

        $response = $this->get(route('documents.links', $attestation));

        $response->assertOk()
            ->assertSee(route('certificat.pdf', $attestation), false)
            ->assertSee(route('attestation.pdf', $attestation), false)
            ->assertSee($attestation->serialNumber());
    }

    public function test_attestation_pdf_is_generated_with_chromium(): void
    {
        $attestation = Attestation::create([
            'apprenti_nom_prenom' => 'Kokou Adje',
            'date_debut_apprentissage' => '2024-01-15',
            'date_fin_apprentissage' => '2026-01-15',
            'specialisations' => 'Menuiserie Aluminium et Vitrerie',
            'date_delivrance' => '2026-09-03',
        ]);

        $response = $this->get(route('attestation.pdf', $attestation));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $response->streamedContent());
    }

    public function test_attestation_duration_is_rendered_in_months_before_one_year(): void
    {
        $attestation = Attestation::make([
            'apprenti_nom_prenom' => 'Kokou Adje',
            'date_debut_apprentissage' => '2024-01-08',
            'date_fin_apprentissage' => '2024-12-20',
            'specialisations' => 'Menuiserie Aluminium',
            'date_delivrance' => '2024-12-21',
        ]);

        $html = view('pdf.attestation', compact('attestation'))->render();

        $this->assertStringContainsString('Onze (11 mois)', $html);
        $this->assertStringNotContainsString('(00 ans)', $html);
    }

    public function test_pdf_templates_render_dates_in_french(): void
    {
        $attestation = Attestation::make([
            'apprenti_nom_prenom' => 'Kokou Adje',
            'date_naissance' => '2002-09-12',
            'lieu_naissance' => 'Lome',
            'nationalite' => 'Togolaise',
            'date_debut_apprentissage' => '2024-01-08',
            'date_fin_apprentissage' => '2025-01-08',
            'date_delivrance' => '2026-09-25',
        ]);

        $attestationHtml = view('pdf.attestation', compact('attestation'))->render();
        $certificatHtml = view('pdf.certificat', compact('attestation'))->render();

        $this->assertStringContainsString('25 Septembre 2026', $attestationHtml);
        $this->assertStringContainsString('25 Septembre 2026', $certificatHtml);
        $this->assertStringNotContainsString('September', $attestationHtml.$certificatHtml);
    }

    public function test_attestation_duration_rounds_partial_months_up_to_one_month(): void
    {
        $attestation = Attestation::make([
            'apprenti_nom_prenom' => 'Kokou Adje',
            'date_debut_apprentissage' => '2024-01-01',
            'date_fin_apprentissage' => '2024-01-14',
            'specialisations' => 'Menuiserie Aluminium',
            'date_delivrance' => '2024-01-15',
        ]);

        $html = view('pdf.attestation', compact('attestation'))->render();

        $this->assertStringContainsString('Un (01 mois)', $html);
        $this->assertStringNotContainsString('0.43333333333333', $html);
    }

    public function test_attestation_pdf_does_not_include_specialisation_data(): void
    {
        $attestation = Attestation::make([
            'apprenti_nom_prenom' => 'Kokou Adje',
            'date_debut_apprentissage' => '2024-01-08',
            'date_fin_apprentissage' => '2024-12-20',
            'specialisations' => 'Donnee a ne pas afficher',
            'date_delivrance' => '2024-12-21',
        ]);

        $html = view('pdf.attestation', compact('attestation'))->render();

        $this->assertStringNotContainsString('Donnee a ne pas afficher', $html);
    }

    public function test_attestation_duration_is_rendered_in_years_from_one_year(): void
    {
        $attestation = Attestation::make([
            'apprenti_nom_prenom' => 'Kokou Adje',
            'date_debut_apprentissage' => '2024-01-08',
            'date_fin_apprentissage' => '2025-01-08',
            'specialisations' => 'Menuiserie Aluminium',
            'date_delivrance' => '2025-01-09',
        ]);

        $html = view('pdf.attestation', compact('attestation'))->render();

        $this->assertStringContainsString('Un (01 an)', $html);
    }

    public function test_attestation_pdf_supports_long_text_and_stamp_fallback(): void
    {
        Config::set('business.stamp_image', null);

        $attestation = Attestation::create([
            'apprenti_nom_prenom' => 'Ama Mensah',
            'date_debut_apprentissage' => '2020-02-01',
            'date_fin_apprentissage' => '2026-08-31',
            'specialisations' => 'Menuiserie Aluminium, Vitrerie, Pose de portes et fenêtres, Fabrication de gardes-corps et mains courantes',
            'date_delivrance' => '2026-09-03',
        ]);

        $response = $this->get(route('attestation.pdf', $attestation));

        $response->assertOk();
        $this->assertStringStartsWith('%PDF-', $response->streamedContent());
    }

    public function test_certificat_pdf_is_generated_without_identity_photo(): void
    {
        $attestation = Attestation::create([
            'apprenti_nom_prenom' => 'Kossi Mensah',
            'date_naissance' => '2002-06-12',
            'lieu_naissance' => 'Lome',
            'nationalite' => 'Togolaise',
            'date_debut_apprentissage' => '2023-01-01',
            'date_fin_apprentissage' => '2025-12-31',
            'specialisations' => 'Vitrerie',
            'date_delivrance' => '2026-09-03',
        ]);

        $response = $this->get(route('certificat.pdf', $attestation));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $response->streamedContent());
    }

    public function test_certificat_template_embeds_uploaded_photo_from_a_storage_path_or_url(): void
    {
        Config::set('filesystems.default', 'public');
        Storage::fake('public');
        Storage::disk('public')->putFileAs(
            'attestations',
            UploadedFile::fake()->image('apprenti.png', 100, 100),
            'apprenti.png',
        );

        $attestation = Attestation::make([
            'apprenti_nom_prenom' => 'Kossi Mensah',
            'date_naissance' => '2002-06-12',
            'lieu_naissance' => 'Lome',
            'nationalite' => 'Togolaise',
            'date_debut_apprentissage' => '2023-01-01',
            'date_fin_apprentissage' => '2025-12-31',
            'date_delivrance' => '2026-09-03',
            'photo_profil' => 'storage/attestations/apprenti.png',
        ]);

        $pathHtml = view('pdf.certificat', compact('attestation'))->render();

        $attestation->photo_profil = Storage::disk('public')->url('attestations/apprenti.png');
        $urlHtml = view('pdf.certificat', compact('attestation'))->render();

        $this->assertStringContainsString('data:image/png;base64,', $pathHtml);
        $this->assertStringContainsString('data:image/png;base64,', $urlHtml);
    }

    public function test_certificate_and_quote_templates_preserve_image_containment_and_signature_lines(): void
    {
        $attestation = Attestation::make([
            'apprenti_nom_prenom' => 'Kossi Mensah',
            'date_naissance' => '2002-06-12',
            'lieu_naissance' => 'Lome',
            'nationalite' => 'Togolaise',
            'date_debut_apprentissage' => '2023-01-01',
            'date_fin_apprentissage' => '2025-12-31',
            'specialisations' => 'Vitrerie',
            'date_delivrance' => '2026-09-03',
        ]);
        $devis = Devis::make([
            'client_nom' => 'Entreprise exemple',
            'client_telephone' => '+228 90 00 00 00',
            'client_ville' => 'Lome',
            'client_pays' => 'Togo',
            'description_chantier' => 'Description',
            'lignes_facturation' => [],
            'montant_main_doeuvre' => 0,
            'acompte_requis_pourcentage' => 0,
        ]);
        $devis->created_at = now();

        $certificateHtml = view('pdf.certificat', compact('attestation'))->render();
        $quoteHtml = view('pdf.devis', compact('devis'))->render();

        $this->assertStringContainsString('object-fit: contain', $certificateHtml);
        $this->assertStringContainsString("font-family: 'Roboto'", $certificateHtml);
        $this->assertStringContainsString('class="signature-line"', $certificateHtml);
        $this->assertStringContainsString('class="signature-line"', $quoteHtml);
        $this->assertStringContainsString('font-size: 12px', $quoteHtml);
    }

    public function test_stamp_images_have_no_template_defined_border(): void
    {
        $attestation = Attestation::make([
            'apprenti_nom_prenom' => 'Kossi Mensah',
            'date_debut_apprentissage' => '2023-01-01',
            'date_fin_apprentissage' => '2025-12-31',
            'specialisations' => 'Vitrerie',
            'date_delivrance' => '2026-09-03',
        ]);

        $attestationHtml = view('pdf.attestation', compact('attestation'))->render();
        $certificateHtml = view('pdf.certificat', compact('attestation'))->render();

        $this->assertDoesNotMatchRegularExpression('/\.stamp-placeholder\s*\{[^}]*border:/s', $attestationHtml);
        $this->assertDoesNotMatchRegularExpression('/\.stamp\s*\{[^}]*border:/s', $certificateHtml);
        $this->assertDoesNotMatchRegularExpression('/\.stamp\.has-stamp\s*\{[^}]*border:/s', $certificateHtml);
    }

    public function test_certificat_duration_is_rendered_in_months_before_one_year(): void
    {
        $attestation = Attestation::make([
            'apprenti_nom_prenom' => 'Kokou Adje',
            'date_naissance' => '2002-06-12',
            'lieu_naissance' => 'Lome',
            'nationalite' => 'Togolaise',
            'date_debut_apprentissage' => '2024-01-08',
            'date_fin_apprentissage' => '2024-12-20',
            'specialisations' => 'Vitrerie',
            'date_delivrance' => '2024-12-21',
        ]);

        $html = view('pdf.certificat', compact('attestation'))->render();

        $this->assertStringContainsString('Onze (11 mois)', $html);
    }

    public function test_devis_pdf_is_generated_with_multiple_billing_lines(): void
    {
        $devis = Devis::create([
            'client_nom' => 'Entreprise exemple',
            'client_telephone' => '+228 90 00 00 00',
            'client_ville' => 'Lome',
            'client_pays' => 'Togo',
            'description_chantier' => 'Remplacement de portes et fenetres pour un chantier professionnel.',
            'lignes_facturation' => [
                ['designation' => 'Porte aluminium vitree', 'quantite' => 3, 'prix_unitaire' => 125000],
                ['designation' => 'Fenetre coulissante', 'quantite' => 4, 'prix_unitaire' => 85000],
            ],
            'montant_main_doeuvre' => 150000,
            'acompte_requis_pourcentage' => 40,
            'statut' => 'brouillon',
        ]);

        $response = $this->get(route('devis.pdf', $devis));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $response->streamedContent());

        $html = view('pdf.devis', compact('devis'))->render();

        $this->assertStringContainsString('<td class="index">1</td>', $html);
        $this->assertStringContainsString('<td class="index">2</td>', $html);
        $this->assertStringNotContainsString('<td class="designation">Main-d’œuvre</td>', $html);
        $this->assertStringContainsString('Total Main d\'œuvre', $html);
        $this->assertStringContainsString('Porte aluminium vitree', $html);
        $this->assertStringContainsString('375 000 FCFA', $html);
        $this->assertStringContainsString('Fenetre coulissante', $html);
        $this->assertStringContainsString('340 000 FCFA', $html);
    }

    public function test_devis_pdf_reconstructs_material_line_for_legacy_quotes(): void
    {
        $devis = Devis::make([
            'client_nom' => 'Ancien client',
            'client_telephone' => '+228 90 00 00 00',
            'client_pays' => 'Togo',
            'description_chantier' => 'Devis historique',
            'lignes_facturation' => null,
            'montant_materiel' => 850000,
            'montant_main_doeuvre' => 150000,
            'montant_total' => 1000000,
            'acompte_requis_pourcentage' => 30,
        ]);
        $devis->created_at = now();

        $html = view('pdf.devis', compact('devis'))->render();

        $this->assertStringContainsString('Matériel / fournitures', $html);
        $this->assertStringContainsString('850 000 FCFA', $html);
    }

    public function test_devis_pdf_supports_long_description_and_additional_pages(): void
    {
        $devis = Devis::create([
            'client_nom' => 'Client avec un chantier de grande ampleur',
            'client_telephone' => '+228 90 00 00 00',
            'client_ville' => 'Lome',
            'client_pays' => 'Togo',
            'description_chantier' => str_repeat('Description détaillée du chantier avec plusieurs contraintes techniques et finitions à prendre en compte. ', 18),
            'lignes_facturation' => collect(range(1, 24))->map(fn (int $index): array => [
                'designation' => 'Prestation '.$index,
                'description' => 'Fourniture, fabrication et pose selon les mesures validées sur le chantier.',
                'quantite' => 1,
                'prix_unitaire' => 25000,
            ])->all(),
            'montant_main_doeuvre' => 300000,
            'acompte_requis_pourcentage' => 40,
            'statut' => 'brouillon',
        ]);

        $response = $this->get(route('devis.pdf', $devis));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $pdf = $response->streamedContent();

        $this->assertStringStartsWith('%PDF-', $pdf);
        $this->assertGreaterThanOrEqual(2, preg_match_all('/\/Type\s*\/Page\b/', $pdf));
    }
}
