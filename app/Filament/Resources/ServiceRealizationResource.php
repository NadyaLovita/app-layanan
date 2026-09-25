<?php

namespace App\Filament\Resources;

use App\Enums\ActivityType;
use App\Enums\ConformityStatus;
use App\Enums\RealizationStatus;
use App\Enums\ValidationResult;
use App\Enums\ValidationStatus;
use App\Enums\VehicleOperationalStatus;
use App\Enums\VolumeUnit;
use App\Filament\Resources\ServiceRealizationResource\Pages;
use App\Filament\Resources\ServiceRealizationResource\RelationManagers;
use App\Models\IssueType;
use App\Models\ServiceRealization;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class ServiceRealizationResource extends Resource
{
    protected static ?string $model = ServiceRealization::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string|UnitEnum|null $navigationGroup = 'Operasional';

    protected static ?string $navigationLabel = 'Realisasi Layanan';

    protected static ?string $modelLabel = 'Realisasi Layanan';

    protected static ?string $pluralModelLabel = 'Realisasi Layanan';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Kegiatan')
                    ->schema([
                        Forms\Components\Select::make('activity_type')
                            ->label('Jenis Kegiatan')
                            ->options(ActivityType::class)
                            ->required()
                            ->native(false)
                            ->default('incidental')
                            ->live(),

                        Forms\Components\DatePicker::make('activity_date')
                            ->label('Tanggal Kegiatan')
                            ->required()
                            ->native(false)
                            ->default(now()),

                        Forms\Components\Select::make('operation_plan_id')
                            ->label('Rencana Operasional')
                            ->relationship('operationPlan', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record): string => "Rencana #{$record->id} — {$record->plan_date->format('d M Y')} — {$record->vehicle->plate_number}")
                            ->searchable()
                            ->preload()
                            ->visible(fn (Get $get): bool => $get('activity_type') === 'planned')
                            ->helperText('Pilih rencana operasional yang mendasari kegiatan ini.'),
                    ])->columns(2),

                Section::make('Armada & Pengemudi')
                    ->schema([
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
                    ])->columns(2),

                Section::make('Waktu & Status')
                    ->schema([
                        Forms\Components\DateTimePicker::make('started_at')
                            ->label('Waktu Mulai')
                            ->native(false)
                            ->seconds(false),

                        Forms\Components\DateTimePicker::make('finished_at')
                            ->label('Waktu Selesai')
                            ->native(false)
                            ->seconds(false),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(RealizationStatus::class)
                            ->required()
                            ->default('planned')
                            ->native(false),
                    ])->columns(3),

                Section::make('Volume & Lokasi Akhir')
                    ->schema([
                        Forms\Components\TextInput::make('total_volume')
                            ->label('Total Volume')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0),

                        Forms\Components\Select::make('volume_unit')
                            ->label('Satuan Volume')
                            ->options(VolumeUnit::class)
                            ->native(false),

                        Forms\Components\Select::make('final_location_id')
                            ->label('Lokasi Akhir (TPA/TPS)')
                            ->relationship('finalLocation', 'name')
                            ->searchable()
                            ->preload(),
                    ])->columns(3),

                Section::make('Rute Aktual')
                    ->description('Wilayah yang benar-benar dilayani. Urutan menunjukkan rute aktual.')
                    ->schema([
                        Forms\Components\Repeater::make('realizationAreas')
                            ->label('')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('service_area_id')
                                    ->label('Wilayah Pelayanan')
                                    ->relationship('serviceArea', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live(),

                                Forms\Components\Select::make('location_id')
                                    ->label('Lokasi Spesifik')
                                    ->relationship(
                                        'location',
                                        'name',
                                        fn (Builder $query, Get $get): Builder => $query
                                            ->when(
                                                $get('service_area_id'),
                                                fn (Builder $q, $serviceAreaId): Builder => $q->where('service_area_id', $serviceAreaId)
                                            )
                                    )
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\TextInput::make('sequence')
                                    ->label('Urutan')
                                    ->numeric()
                                    ->required()
                                    ->minValue(1),

                                Forms\Components\DateTimePicker::make('arrived_at')
                                    ->label('Waktu Tiba')
                                    ->native(false)
                                    ->seconds(false),

                                Forms\Components\TextInput::make('volume')
                                    ->label('Volume')
                                    ->numeric()
                                    ->step(0.01)
                                    ->minValue(0),

                                Forms\Components\Textarea::make('notes')
                                    ->label('Catatan')
                                    ->rows(2),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->reorderable()
                            ->orderColumn('sequence')
                            ->addActionLabel('Tambah Wilayah'),
                    ]),

                Section::make('Keterangan')
                    ->schema([
                        Forms\Components\Textarea::make('field_condition')
                            ->label('Kondisi Lapangan')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('conformity_status')
                            ->label('Status Kesesuaian')
                            ->options(ConformityStatus::class)
                            ->default('unplanned')
                            ->native(false)
                            ->visible(fn (Get $get): bool => $get('activity_type') === 'planned'),

                        Forms\Components\Textarea::make('change_reason')
                            ->label('Alasan Perubahan')
                            ->rows(3)
                            ->visible(fn (Get $get): bool => in_array($get('conformity_status'), ['changed', 'not_executed'])),

                        Forms\Components\Select::make('validation_status')
                            ->label('Status Validasi')
                            ->options(ValidationStatus::class)
                            ->default('pending')
                            ->native(false)
                            ->disabled(),

                        Forms\Components\Hidden::make('created_by')
                            ->default(fn (): ?int => auth()->id()),

                        Forms\Components\Hidden::make('updated_by'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('activity_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('activity_type')
                    ->label('Jenis')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('vehicle.plate_number')
                    ->label('Armada')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('driver.name')
                    ->label('Pengemudi')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_volume')
                    ->label('Volume')
                    ->formatStateUsing(fn ($record): string => $record->total_volume
                        ? number_format($record->total_volume, 2).' '.($record->volume_unit?->getLabel() ?? '')
                        : '-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('validation_status')
                    ->label('Validasi')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('started_at')
                    ->label('Mulai')
                    ->dateTime('H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('realization_areas_count')
                    ->label('Jml. Wilayah')
                    ->counts('realizationAreas')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('conformity_status')
                    ->label('Kesesuaian')
                    ->badge()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('activity_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(RealizationStatus::class),

                Tables\Filters\SelectFilter::make('activity_type')
                    ->label('Jenis Kegiatan')
                    ->options(ActivityType::class),

                Tables\Filters\SelectFilter::make('validation_status')
                    ->label('Status Validasi')
                    ->options(ValidationStatus::class),

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

                Tables\Filters\Filter::make('activity_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn (Builder $q, $date): Builder => $q->whereDate('activity_date', '>=', $date))
                            ->when($data['until'], fn (Builder $q, $date): Builder => $q->whereDate('activity_date', '<=', $date));
                    }),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Actions\ActionGroup::make([
                    Actions\Action::make('mulaiOperasional')
                        ->label('Mulai Operasional')
                        ->icon('heroicon-o-play')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('Mulai Operasional')
                        ->modalDescription('Tandai kegiatan ini sebagai sedang berjalan?')
                        ->visible(fn (ServiceRealization $record): bool => $record->status === RealizationStatus::Planned)
                        ->action(function (ServiceRealization $record): void {
                            $record->update([
                                'status' => RealizationStatus::Running,
                                'started_at' => now(),
                                'updated_by' => auth()->id(),
                            ]);

                            Notification::make()
                                ->title('Operasional dimulai')
                                ->success()
                                ->send();
                        }),

                    Actions\Action::make('selesai')
                        ->label('Selesai')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Selesaikan Kegiatan')
                        ->modalDescription('Tandai kegiatan ini sebagai selesai?')
                        ->visible(fn (ServiceRealization $record): bool => $record->status === RealizationStatus::Running)
                        ->action(function (ServiceRealization $record): void {
                            $record->update([
                                'status' => RealizationStatus::Completed,
                                'finished_at' => now(),
                                'updated_by' => auth()->id(),
                            ]);

                            Notification::make()
                                ->title('Kegiatan selesai')
                                ->success()
                                ->send();
                        }),

                    Actions\Action::make('terkendala')
                        ->label('Terkendala')
                        ->icon('heroicon-o-exclamation-triangle')
                        ->color('danger')
                        ->visible(fn (ServiceRealization $record): bool => in_array($record->status, [RealizationStatus::Running, RealizationStatus::Planned]))
                        ->form([
                            Forms\Components\Select::make('issue_type_id')
                                ->label('Jenis Kendala')
                                ->relationship('issues.issueType', 'name')
                                ->options(fn () => IssueType::active()->pluck('name', 'id'))
                                ->required(),

                            Forms\Components\Textarea::make('description')
                                ->label('Deskripsi Kendala')
                                ->required()
                                ->rows(3),
                        ])
                        ->action(function (ServiceRealization $record, array $data): void {
                            $record->update([
                                'status' => RealizationStatus::Obstructed,
                                'updated_by' => auth()->id(),
                            ]);

                            $record->issues()->create([
                                'issue_type_id' => $data['issue_type_id'],
                                'description' => $data['description'],
                                'occurred_at' => now(),
                                'follow_up_status' => 'open',
                                'created_by' => auth()->id(),
                            ]);

                            Notification::make()
                                ->title('Kendala dicatat')
                                ->warning()
                                ->send();
                        }),

                    Actions\Action::make('validasi')
                        ->label('Validasi')
                        ->icon('heroicon-o-shield-check')
                        ->color('primary')
                        ->visible(fn (ServiceRealization $record): bool => $record->validation_status === ValidationStatus::Pending
                            && $record->status === RealizationStatus::Completed)
                        ->form([
                            Forms\Components\Select::make('result')
                                ->label('Hasil Validasi')
                                ->options(ValidationResult::class)
                                ->required()
                                ->native(false),

                            Forms\Components\Textarea::make('notes')
                                ->label('Catatan Validasi')
                                ->rows(3),
                        ])
                        ->action(function (ServiceRealization $record, array $data): void {
                            $record->validations()->create([
                                'validated_by' => auth()->id(),
                                'result' => $data['result'],
                                'notes' => $data['notes'] ?? null,
                                'validated_at' => now(),
                            ]);

                            $record->update([
                                'validation_status' => $data['result'] === 'valid'
                                    ? ValidationStatus::Valid
                                    : ValidationStatus::NeedsRevision,
                                'updated_by' => auth()->id(),
                            ]);

                            Notification::make()
                                ->title('Validasi berhasil disimpan')
                                ->success()
                                ->send();
                        }),

                    Actions\ViewAction::make(),
                    Actions\EditAction::make(),
                    Actions\DeleteAction::make(),
                    Actions\RestoreAction::make(),
                ]),
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
        return [
            RelationManagers\OperationalIssuesRelationManager::class,
            RelationManagers\AttachmentsRelationManager::class,
            RelationManagers\ValidationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceRealizations::route('/'),
            'create' => Pages\CreateServiceRealization::route('/create'),
            'view' => Pages\ViewServiceRealization::route('/{record}'),
            'edit' => Pages\EditServiceRealization::route('/{record}/edit'),
        ];
    }
}
