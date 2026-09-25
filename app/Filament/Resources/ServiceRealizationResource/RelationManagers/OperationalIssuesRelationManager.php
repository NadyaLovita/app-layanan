<?php

namespace App\Filament\Resources\ServiceRealizationResource\RelationManagers;

use App\Enums\FollowUpStatus;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class OperationalIssuesRelationManager extends RelationManager
{
    protected static string $relationship = 'issues';

    protected static ?string $title = 'Kendala Operasional';

    protected static ?string $modelLabel = 'Kendala';

    protected static ?string $pluralModelLabel = 'Kendala';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('issue_type_id')
                    ->label('Jenis Kendala')
                    ->relationship('issueType', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\DateTimePicker::make('occurred_at')
                    ->label('Waktu Kejadian')
                    ->required()
                    ->native(false)
                    ->seconds(false)
                    ->default(now()),

                Forms\Components\Select::make('follow_up_status')
                    ->label('Status Tindak Lanjut')
                    ->options(FollowUpStatus::class)
                    ->required()
                    ->default('open')
                    ->native(false),

                Forms\Components\Textarea::make('follow_up')
                    ->label('Keterangan Tindak Lanjut')
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\Hidden::make('created_by')
                    ->default(fn (): ?int => auth()->id()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('issueType.name')
                    ->label('Jenis Kendala')
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50),

                Tables\Columns\TextColumn::make('occurred_at')
                    ->label('Waktu Kejadian')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('follow_up_status')
                    ->label('Tindak Lanjut')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Dilaporkan oleh')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('occurred_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('follow_up_status')
                    ->label('Status')
                    ->options(FollowUpStatus::class),
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['created_by'] = auth()->id();

                        return $data;
                    }),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
