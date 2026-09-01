<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Devis extends Model
{
    protected $fillable = [
        'numero_devis',
        'client_nom',
        'client_telephone',
        'client_ville',
        'client_pays',
        'description_chantier',
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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $devis) {
            $devis->numero_devis = 'DEV-'.str_pad((string) ((int) self::query()->max('id') + 1), 5, '0', STR_PAD_LEFT);
        });

        static::saving(function (self $devis) {
            $devis->montant_total = (float) ($devis->montant_materiel ?? 0) + (float) ($devis->montant_main_doeuvre ?? 0);
        });
    }
}
