<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttestationResource\Pages;
use App\Models\Attestation;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AttestationResource extends Resource
{
    protected static ?string $model = Attestation::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    protected static ?string $navigationGroup = 'Gestion';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Attestation')->schema([
                    Forms\Components\TextInput::make('numero_attestation')
                        ->label('Numéro d’attestation')
                        ->readOnly()
                        ->dehydrated(false)
                        ->visibleOn('edit'),
                    Forms\Components\TextInput::make('apprenti_nom_prenom')
                        ->label('Nom et prénom de l’apprenti')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\DatePicker::make('date_debut_apprentissage')
                            ->label('Date de début')
                            ->required(),
                        Forms\Components\DatePicker::make('date_fin_apprentissage')
                            ->label('Date de fin')
                            ->required(),
                    ]),
                    Forms\Components\TextInput::make('specialisations')
                        ->label('Spécialisation(s)')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\DatePicker::make('date_delivrance')
                        ->label('Date de délivrance')
                        ->required(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero_attestation')
                    ->label('N° attestation')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('apprenti_nom_prenom')
                    ->label('Apprenti')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('specialisations')
                    ->label('Spécialisation')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_debut_apprentissage')
                    ->label('Début')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_fin_apprentissage')
                    ->label('Fin')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_delivrance')
                    ->label('Délivrée le')
                    ->date()
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\Action::make('telecharger_pdf')
                    ->label('Télécharger PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (Attestation $record): string => route('attestation.pdf', ['attestation' => $record]))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttestations::route('/'),
            'create' => Pages\CreateAttestation::route('/create'),
            'edit' => Pages\EditAttestation::route('/{record}/edit'),
        ];
    }
}
