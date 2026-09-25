<?php

namespace App\Filament\Resources;

use App\Enums\VehicleOperationalStatus;
use App\Filament\Resources\OperationPlanResource\Pages;
use App\Models\OperationPlan;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class OperationPlanResource extends Resource
{
    protected static ?string $model = OperationPlan::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|UnitEnum|null $navigationGroup = 'Operasional';

    protected static ?string $navigationLabel = 'Rencana Operasional';

    protected static ?string $modelLabel = 'Rencana Operasional';

    protected static ?string $pluralModelLabel = 'Rencana Operasional';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Rencana')
                    ->schema([
                        Forms\Components\DatePicker::make('plan_date')
                            ->label('Tanggal Rencana')
                            ->required()
                            ->native(false)
                            ->default(now()),

                        Forms\Components\Select::make('vehicle_id')
                            ->label('Armada')
                            ->relationship(
                                'vehicle',
                                'plate_number',
                                fn (Builder $query): Builder => $query
                                    ->where('operational_status', VehicleOperationalStatus::Active->value)
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('driver_id')
                            ->label('Pengemudi')
                            ->relationship(
                                'driver',
                                'name',
                                fn (Builder $query): Builder => $query->where('is_active', true)
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Hidden::make('created_by')
                            ->default(fn (): ?int => auth()->id()),
                    ])->columns(2),

                Section::make('Wilayah Rencana')
                    ->description('Tentukan wilayah pelayanan yang direncanakan dan urutan kunjungannya.')
                    ->schema([
                        Forms\Components\Repeater::make('planAreas')
                            ->label('')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('service_area_id')
                                    ->label('Wilayah Pelayanan')
                                    ->relationship('serviceArea', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('sequence')
                                    ->label('Urutan')
                                    ->numeric()
                                    ->required()
                                    ->default(fn ($get): int => count($get('../../planAreas') ?? []) + 1)
                                    ->minValue(1),
                            ])
                            ->columns(3)
                            ->defaultItems(1)
                            ->reorderable()
                            ->orderColumn('sequence')
                            ->addActionLabel('Tambah Wilayah'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('plan_date')
                    ->label('Tanggal Rencana')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('vehicle.plate_number')
                    ->label('Armada')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('driver.name')
                    ->label('Pengemudi')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('plan_areas_count')
                    ->label('Jml. Wilayah')
                    ->counts('planAreas')
                    ->sortable(),

                Tables\Columns\TextColumn::make('service_realizations_count')
                    ->label('Realisasi')
                    ->counts('serviceRealizations')
                    ->sortable(),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Dibuat oleh')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('plan_date', 'desc')
            ->filters([
                Tables\Filters\Filter::make('plan_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn (Builder $q, $date): Builder => $q->whereDate('plan_date', '>=', $date))
                            ->when($data['until'], fn (Builder $q, $date): Builder => $q->whereDate('plan_date', '<=', $date));
                    }),

                Tables\Filters\SelectFilter::make('vehicle_id')
                    ->label('Armada')
                    ->relationship('vehicle', 'plate_number')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('driver_id')
                    ->label('Pengemudi')
                    ->relationship('driver', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
                Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                    Actions\ForceDeleteBulkAction::make(),
                    Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOperationPlans::route('/'),
            'create' => Pages\CreateOperationPlan::route('/create'),
            'view' => Pages\ViewOperationPlan::route('/{record}'),
            'edit' => Pages\EditOperationPlan::route('/{record}/edit'),
        ];
    }
}
