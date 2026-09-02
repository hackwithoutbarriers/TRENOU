<?php

namespace App\Filament\Resources\AttestationResource\Pages;

use App\Filament\Resources\AttestationResource;
use App\Models\Attestation;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAttestation extends EditRecord
{
    protected static string $resource = AttestationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('telecharger_pdf')
                ->label('Télécharger PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn (Attestation $record): string => route('attestation.pdf', ['attestation' => $record]))
                ->openUrlInNewTab(),
            Actions\DeleteAction::make(),
        ];
    }
}
