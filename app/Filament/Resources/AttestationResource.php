<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttestationResource\Pages;
use App\Models\Attestation;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AttestationResource extends Resource
{
    protected static ?string $model = Attestation::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    protected static ?string $navigationGroup = 'Gestion';

    protected static ?string $navigationLabel = 'Attestations de travail';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type_document', 'attestation_travail');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Document')->schema([
                    Forms\Components\TextInput::make('numero_attestation')
                        ->label('Numéro d’attestation')
                        ->readOnly()
                        ->dehydrated(false)
                        ->visibleOn('edit'),
                    Forms\Components\Select::make('type_document')
                        ->label('Type de document')
                        ->options([
                            'certificat' => 'Certificat de fin d’apprentissage',
                            'attestation_travail' => 'Attestation de travail',
                        ])
                        ->default('attestation_travail')
                        ->disabled()
                        ->dehydrated(),
                    Forms\Components\TextInput::make('apprenti_nom_prenom')
                        ->label('Nom et prénom')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\FileUpload::make('photo_profil')
                        ->label('Photo de l’apprenti')
                        ->image()
                        ->avatar()
                        ->disk('public')
                        ->directory('attestations')
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->maxSize(2048)
                        ->imageEditor()
                        ->imageEditorAspectRatios(['1:1'])
                        ->imageCropAspectRatio('1:1')
                        ->imageResizeMode('force')
                        ->imageResizeTargetWidth(600)
                        ->imageResizeTargetHeight(600)
                        ->visible(fn (Get $get): bool => $get('type_document') === 'certificat'),
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\DatePicker::make('date_naissance')
                            ->label('Date de naissance')
                            ->visible(fn (Get $get): bool => $get('type_document') === 'certificat')
                            ->required(fn (Get $get): bool => $get('type_document') === 'certificat'),
                        Forms\Components\TextInput::make('lieu_naissance')
                            ->label('Lieu de naissance')
                            ->visible(fn (Get $get): bool => $get('type_document') === 'certificat')
                            ->required(fn (Get $get): bool => $get('type_document') === 'certificat')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('nationalite')
                            ->label('Nationalité')
                            ->default('Togolaise')
                            ->visible(fn (Get $get): bool => $get('type_document') === 'certificat')
                            ->required(fn (Get $get): bool => $get('type_document') === 'certificat')
                            ->maxLength(255),
                    ]),
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\DatePicker::make('date_debut_apprentissage')
                            ->label('Date de début de travail / apprentissage')
                            ->required(),
                        Forms\Components\DatePicker::make('date_fin_apprentissage')
                            ->label('Date de fin de travail / apprentissage')
                            ->required(),
                    ]),
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
                    ->label('Attestation de travail')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (Attestation $record): string => route('attestation.pdf', ['attestation' => $record]))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('telecharger_certificat')
                    ->label('Certificat de fin d’apprentissage')
                    ->icon('heroicon-o-academic-cap')
                    ->url(fn (Attestation $record): string => route('certificat.pdf', ['attestation' => $record]))
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
