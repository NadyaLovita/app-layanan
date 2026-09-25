<?php

namespace App\Filament\Resources;

use App\Enums\VehicleOperationalStatus;
use App\Filament\Resources\VehicleResource\Pages;
use App\Models\Vehicle;
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

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-truck';

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Armada';

    protected static ?string $modelLabel = 'Armada';

    protected static ?string $pluralModelLabel = 'Armada';

    protected static ?int $navigationSort = 6;

    protected static ?string $recordTitleAttribute = 'plate_number';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Armada')
                    ->schema([
                        Forms\Components\TextInput::make('plate_number')
                            ->label('Nomor Polisi / Identitas')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20),

                        Forms\Components\TextInput::make('type')
                            ->label('Jenis Kendaraan')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('Dump Truck, Arm Roll, Pick Up, dll.'),

                        Forms\Components\TextInput::make('capacity')
                            ->label('Kapasitas')
                            ->numeric()
                            ->step(0.01),

                        Forms\Components\TextInput::make('capacity_unit')
                            ->label('Satuan Kapasitas')
                            ->maxLength(20)
                            ->placeholder('ton, m³, dll.'),

                        Forms\Components\Select::make('operational_status')
                            ->label('Status Operasional')
                            ->options(VehicleOperationalStatus::class)
                            ->required()
                            ->default('active')
                            ->native(false),

                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('plate_number')
                    ->label('No. Polisi')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('type')
                    ->label('Jenis')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('capacity')
                    ->label('Kapasitas')
                    ->formatStateUsing(fn ($record): string => $record->capacity
                        ? number_format($record->capacity, 2).' '.($record->capacity_unit ?? '')
                        : '-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('operational_status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('service_realizations_count')
                    ->label('Total Kegiatan')
                    ->counts('serviceRealizations')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('plate_number')
            ->filters([
                Tables\Filters\SelectFilter::make('operational_status')
                    ->label('Status Operasional')
                    ->options(VehicleOperationalStatus::class),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
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
            'index' => Pages\ListVehicles::route('/'),
            'create' => Pages\CreateVehicle::route('/create'),
            'edit' => Pages\EditVehicle::route('/{record}/edit'),
        ];
    }
}
