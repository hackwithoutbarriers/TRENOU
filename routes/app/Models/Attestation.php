<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attestation extends Model
{
    protected $fillable = [
        'numero_attestation',
        'apprenti_nom_prenom',
        'date_debut_apprentissage',
        'date_fin_apprentissage',
        'specialisations',
        'date_delivrance',
    ];

    protected $casts = [
        'date_debut_apprentissage' => 'date',
        'date_fin_apprentissage' => 'date',
        'date_delivrance' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $attestation) {
            $attestation->numero_attestation = 'ATT-'.str_pad((string) ((int) self::query()->max('id') + 1), 5, '0', STR_PAD_LEFT);
        });
    }
}
