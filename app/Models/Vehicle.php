<?php

namespace App\Models;

use App\Enums\VehicleOperationalStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'plate_number',
        'type',
        'capacity',
        'capacity_unit',
        'operational_status',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'capacity' => 'decimal:2',
            'operational_status' => VehicleOperationalStatus::class,
        ];
    }

    /**
     * Scope a query to only include operational (active) vehicles.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('operational_status', VehicleOperationalStatus::Active->value);
    }

    /**
     * Scope a query to filter by operational status.
     */
    public function scopeOperationalStatus(Builder $query, VehicleOperationalStatus|string $status): Builder
    {
        $statusValue = $status instanceof VehicleOperationalStatus ? $status->value : $status;

        return $query->where('operational_status', $statusValue);
    }

    /**
     * Operation plans assigned to this vehicle.
     */
    public function operationPlans(): HasMany
    {
        return $this->hasMany(OperationPlan::class);
    }

    /**
     * Service realizations performed by this vehicle.
     */
    public function serviceRealizations(): HasMany
    {
        return $this->hasMany(ServiceRealization::class);
    }
}
