<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Devis extends Model
{
    protected $fillable = [
        'numero_devis',
        'client_nom',
        'client_telephone',
        'client_ville',
        'client_pays',
        'description_chantier',
        'lignes_facturation',
        'montant_materiel',
        'montant_main_doeuvre',
        'montant_total',
        'acompte_requis_pourcentage',
        'statut',
    ];

    protected $casts = [
        'montant_materiel' => 'decimal:2',
        'montant_main_doeuvre' => 'decimal:2',
        'montant_total' => 'decimal:2',
        'acompte_requis_pourcentage' => 'integer',
        'lignes_facturation' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $devis) {
            $devis->numero_devis = 'DEV-'.str_pad((string) ((int) self::query()->max('id') + 1), 5, '0', STR_PAD_LEFT);
        });

        static::saving(function (self $devis) {
            $lignes = is_array($devis->lignes_facturation) ? $devis->lignes_facturation : [];
            $totalPrestations = collect($lignes)->sum(function (array $ligne): float {
                return (float) ($ligne['quantite'] ?? 0) * (float) ($ligne['prix_unitaire'] ?? 0);
            });

            $devis->montant_materiel = $totalPrestations;
            $devis->montant_total = $totalPrestations + (float) ($devis->montant_main_doeuvre ?? 0);
        });

        static::updated(function (self $devis) {
            if ($devis->wasChanged('statut') && $devis->statut === 'livre') {
                $devis->sendReviewRequest();
            }
        });
    }

    /**
     * @return array<int, array{designation: string, description?: string, quantite: float|int, prix_unitaire: float|int, total?: float|int}>
     */
    public function billingLines(): array
    {
        $lines = is_array($this->lignes_facturation) ? array_values($this->lignes_facturation) : [];

        if ($lines !== [] || (float) $this->montant_materiel <= 0) {
            return $lines;
        }

        return [[
            'designation' => 'Matériel / fournitures',
            'description' => 'Montant matériel du devis',
            'quantite' => 1,
            'prix_unitaire' => (float) $this->montant_materiel,
            'total' => (float) $this->montant_materiel,
        ]];
    }

    public function temoignages(): HasMany
    {
        return $this->hasMany(Temoignage::class);
    }

    public function sendReviewRequest(): void
    {
        if (! $this->client_telephone || ! $this->client_nom) {
            return;
        }

        $googleUrl = config('services.google.place_id')
            ? 'https://www.google.com/search?q='.urlencode('TRENOU '.($this->client_ville ?? 'Togo'))
            : 'https://www.google.com/maps';

        $internalUrl = route('reviews.share', ['client' => $this->client_nom, 'projet_ref' => $this->numero_devis], false);
        $message = sprintf(
            'Bonjour %s, merci pour votre confiance. Merci de laisser un avis Google : %s. Et pour partager un témoignage photo, remplissez ce formulaire : %s',
            $this->client_nom,
            $googleUrl,
            $internalUrl
        );

        $number = config('services.whatsapp.number', '22890585976');
        $waLink = 'https://wa.me/'.$number.'?text='.urlencode($message);

        logger()->info('Review request sent', [
            'devis_id' => $this->id,
            'client' => $this->client_nom,
            'project_ref' => $this->numero_devis,
            'google_url' => $googleUrl,
            'internal_url' => $internalUrl,
            'whatsapp_url' => $waLink,
        ]);
    }
}
