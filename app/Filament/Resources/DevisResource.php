<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DevisResource\Pages;
use App\Models\Devis;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DevisResource extends Resource
{
    protected static ?string $model = Devis::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Gestion';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Client')->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('client_nom')
                            ->label('Nom du client')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('client_telephone')
                            ->label('Téléphone')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                    ]),
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('client_ville')
                            ->label('Ville du client')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('client_pays')
                            ->label('Pays du client')
                            ->default('Togo')
                            ->required()
                            ->maxLength(255),
                    ]),
                ]),

                Section::make('Devis')->schema([
                    Forms\Components\TextInput::make('numero_devis')
                        ->label('Numéro du devis')
                        ->readOnly()
                        ->dehydrated(false)
                        ->visibleOn('edit'),

                    Forms\Components\Textarea::make('description_chantier')
                        ->label('Description du chantier')
                        ->required()
                        ->rows(5)
                        ->columnSpanFull(),

                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('montant_materiel')
                            ->label('Matériel')
                            ->required()
                            ->numeric()
                            ->prefix('FCFA ')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                $set('montant_total', ((float) ($get('montant_materiel') ?? 0)) + ((float) ($get('montant_main_doeuvre') ?? 0)));
                            }),
                        Forms\Components\TextInput::make('montant_main_doeuvre')
                            ->label('Main-d’œuvre')
                            ->required()
                            ->numeric()
                            ->prefix('FCFA ')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                $set('montant_total', ((float) ($get('montant_materiel') ?? 0)) + ((float) ($get('montant_main_doeuvre') ?? 0)));
                            }),
                    ]),

                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('acompte_requis_pourcentage')
                            ->label('Acompte requis (%)')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(100),
                        Forms\Components\Select::make('statut')
                            ->label('Statut')
                            ->options([
                                'brouillon' => 'Brouillon',
                                'envoye' => 'Envoyé',
                                'accepte' => 'Accepté',
                                'refuse' => 'Refusé',
                            ])
                            ->default('brouillon')
                            ->required(),
                    ]),

                    Forms\Components\TextInput::make('montant_total')
                        ->label('Total HT')
                        ->numeric()
                        ->prefix('FCFA ')
                        ->disabled()
                        ->dehydrated(false)
                        ->formatStateUsing(function ($state, $record) {
                            $value = $state ?? (($record?->montant_materiel ?? 0) + ($record?->montant_main_doeuvre ?? 0));

                            return number_format((float) $value, 2, ',', ' ');
                        }),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero_devis')
                    ->label('N° devis')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('client_nom')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('client_telephone')
                    ->label('Téléphone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('client_ville')
                    ->label('Ville')
                    ->searchable(),
                Tables\Columns\TextColumn::make('montant_total')
                    ->label('Total HT')
                    ->money('XOF')
                    ->sortable(),
                Tables\Columns\TextColumn::make('acompte_requis_pourcentage')
                    ->label('Acompte')
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('statut')
                    ->label('Statut')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'brouillon' => 'gray',
                        'envoye' => 'warning',
                        'accepte' => 'success',
                        'refuse' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'brouillon' => 'Brouillon',
                        'envoye' => 'Envoyé',
                        'accepte' => 'Accepté',
                        'refuse' => 'Refusé',
                        default => ucfirst($state),
                    }),
            ])
            ->filters([
                SelectFilter::make('statut')
                    ->label('Statut')
                    ->options([
                        'brouillon' => 'Brouillon',
                        'envoye' => 'Envoyé',
                        'accepte' => 'Accepté',
                        'refuse' => 'Refusé',
                    ]),
                SelectFilter::make('client_pays')
                    ->label('Pays')
                    ->options(Devis::query()->pluck('client_pays', 'client_pays')->unique()->sort()->toArray()),
            ])
            ->actions([
                Tables\Actions\Action::make('telecharger_pdf')
                    ->label('Télécharger PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (Devis $record): string => route('devis.pdf', ['devis' => $record]))
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
            'index' => Pages\ListDevis::route('/'),
            'create' => Pages\CreateDevis::route('/create'),
            'edit' => Pages\EditDevis::route('/{record}/edit'),
        ];
    }
}
