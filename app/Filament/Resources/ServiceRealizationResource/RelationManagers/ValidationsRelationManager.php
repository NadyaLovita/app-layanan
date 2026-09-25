<?php

namespace App\Filament\Resources\ServiceRealizationResource\RelationManagers;

use App\Enums\ValidationResult;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ValidationsRelationManager extends RelationManager
{
    protected static string $relationship = 'validations';

    protected static ?string $title = 'Riwayat Validasi';

    protected static ?string $modelLabel = 'Validasi';

    protected static ?string $pluralModelLabel = 'Validasi';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('result')
                    ->label('Hasil Validasi')
                    ->options(ValidationResult::class)
                    ->required()
                    ->native(false),

                Forms\Components\Textarea::make('notes')
                    ->label('Catatan Validasi')
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\DateTimePicker::make('validated_at')
                    ->label('Waktu Validasi')
                    ->required()
                    ->native(false)
                    ->seconds(false)
                    ->default(now()),

                Forms\Components\Hidden::make('validated_by')
                    ->default(fn (): ?int => auth()->id()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('result')
                    ->label('Hasil')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('notes')
                    ->label('Catatan')
                    ->limit(60)
                    ->placeholder('(tanpa catatan)'),

                Tables\Columns\TextColumn::make('validator.name')
                    ->label('Validator'),

                Tables\Columns\TextColumn::make('validated_at')
                    ->label('Waktu Validasi')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('validated_at', 'desc')
            ->headerActions([
                Actions\CreateAction::make()
                    ->label('Tambah Validasi')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['validated_by'] = auth()->id();

                        return $data;
                    })
                    ->after(function ($record): void {
                        // Update parent realization validation_status
                        $realization = $this->getOwnerRecord();
                        $realization->update([
                            'validation_status' => $record->result === ValidationResult::Valid
                                ? 'valid'
                                : 'needs_revision',
                            'updated_by' => auth()->id(),
                        ]);
                    }),
            ])
            ->actions([
                Actions\ViewAction::make(),
            ]);
    }
}
