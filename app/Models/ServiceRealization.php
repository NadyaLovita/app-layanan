<?php

namespace App\Models;

use App\Enums\ActivityType;
use App\Enums\ConformityStatus;
use App\Enums\RealizationStatus;
use App\Enums\ValidationStatus;
use App\Enums\VolumeUnit;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceRealization extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'operation_plan_id',
        'activity_type',
        'activity_date',
        'started_at',
        'finished_at',
        'vehicle_id',
        'driver_id',
        'status',
        'total_volume',
        'volume_unit',
        'final_location_id',
        'field_condition',
        'conformity_status',
        'change_reason',
        'validation_status',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'activity_type' => ActivityType::class,
            'activity_date' => 'date',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
            'status' => RealizationStatus::class,
            'total_volume' => 'decimal:2',
            'volume_unit' => VolumeUnit::class,
            'conformity_status' => ConformityStatus::class,
            'validation_status' => ValidationStatus::class,
        ];
    }

    /**
     * Check if this activity is planned.
     */
    public function isPlanned(): bool
    {
        return $this->activity_type === ActivityType::Planned;
    }

    /**
     * Check if this activity is incidental / unplanned.
     */
    public function isIncidental(): bool
    {
        return $this->activity_type === ActivityType::Incidental;
    }

    /**
     * Check if this activity is currently running.
     */
    public function isRunning(): bool
    {
        return $this->status === RealizationStatus::Running;
    }

    /**
     * Check if this activity is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === RealizationStatus::Completed;
    }

    /**
     * Check if this activity is currently obstructed.
     */
    public function isObstructed(): bool
    {
        return $this->status === RealizationStatus::Obstructed;
    }

    /**
     * Scope query for today's realizations.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('activity_date', today());
    }

    /**
     * Scope query for a specific activity date.
     */
    public function scopeForDate(Builder $query, CarbonInterface|string $date): Builder
    {
        return $query->whereDate('activity_date', $date);
    }

    /**
     * Scope query for a date range.
     */
    public function scopeBetweenDates(Builder $query, CarbonInterface|string $startDate, CarbonInterface|string $endDate): Builder
    {
        return $query->whereBetween('activity_date', [$startDate, $endDate]);
    }

    /**
     * Scope query by status.
     */
    public function scopeStatus(Builder $query, RealizationStatus|string $status): Builder
    {
        $statusValue = $status instanceof RealizationStatus ? $status->value : $status;

        return $query->where('status', $statusValue);
    }

    /**
     * Scope query for running realizations.
     */
    public function scopeRunning(Builder $query): Builder
    {
        return $query->where('status', RealizationStatus::Running->value);
    }

    /**
     * Scope query for completed realizations.
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', RealizationStatus::Completed->value);
    }

    /**
     * Scope query for obstructed realizations.
     */
    public function scopeObstructed(Builder $query): Builder
    {
        return $query->where('status', RealizationStatus::Obstructed->value);
    }

    /**
     * Scope query by activity type.
     */
    public function scopeActivityType(Builder $query, ActivityType|string $type): Builder
    {
        $typeValue = $type instanceof ActivityType ? $type->value : $type;

        return $query->where('activity_type', $typeValue);
    }

    /**
     * Scope query for planned activities.
     */
    public function scopePlanned(Builder $query): Builder
    {
        return $query->where('activity_type', ActivityType::Planned->value);
    }

    /**
     * Scope query for incidental activities.
     */
    public function scopeIncidental(Builder $query): Builder
    {
        return $query->where('activity_type', ActivityType::Incidental->value);
    }

    /**
     * Scope query for valid realizations.
     */
    public function scopeValid(Builder $query): Builder
    {
        return $query->where('validation_status', ValidationStatus::Valid->value);
    }

    /**
     * Optional operation plan referenced by this realization.
     */
    public function operationPlan(): BelongsTo
    {
        return $this->belongsTo(OperationPlan::class);
    }

    /**
     * Vehicle executing this realization.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Driver executing this realization.
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * Final dump/processing location (e.g. TPA).
     */
    public function finalLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'final_location_id');
    }

    /**
     * User who created this record.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * User who last updated this record.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Route stops / actual service areas visited.
     */
    public function realizationAreas(): HasMany
    {
        return $this->hasMany(ServiceRealizationArea::class)->orderBy('sequence');
    }

    /**
     * Distinct service areas visited during this realization.
     */
    public function serviceAreas(): BelongsToMany
    {
        return $this->belongsToMany(ServiceArea::class, 'service_realization_areas')
            ->withPivot(['id', 'location_id', 'sequence', 'arrived_at', 'volume', 'notes'])
            ->withTimestamps()
            ->orderByPivot('sequence');
    }

    /**
     * Operational issues reported during this realization.
     */
    public function issues(): HasMany
    {
        return $this->hasMany(OperationalIssue::class);
    }

    /**
     * Documentation/photos attached to this realization.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    /**
     * Validations performed on this realization.
     */
    public function validations(): HasMany
    {
        return $this->hasMany(ServiceRealizationValidation::class)->orderByDesc('validated_at');
    }

    /**
     * Latest validation performed on this realization.
     */
    public function latestValidation(): HasOne
    {
        return $this->hasOne(ServiceRealizationValidation::class)->latestOfMany('validated_at');
    }
}
