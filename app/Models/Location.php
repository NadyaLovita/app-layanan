<?php

namespace App\Models;

use App\Enums\LocationType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'service_area_id',
        'name',
        'type',
        'address',
        'latitude',
        'longitude',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => LocationType::class,
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Scope a query to only include active locations.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by location type.
     */
    public function scopeType(Builder $query, LocationType|string $type): Builder
    {
        $typeValue = $type instanceof LocationType ? $type->value : $type;

        return $query->where('type', $typeValue);
    }

    /**
     * Service area where this location belongs.
     */
    public function serviceArea(): BelongsTo
    {
        return $this->belongsTo(ServiceArea::class);
    }

    /**
     * Realizations where this location was the final destination (e.g. TPA).
     */
    public function finalServiceRealizations(): HasMany
    {
        return $this->hasMany(ServiceRealization::class, 'final_location_id');
    }

    /**
     * Realization route stops at this specific location.
     */
    public function serviceRealizationAreas(): HasMany
    {
        return $this->hasMany(ServiceRealizationArea::class);
    }
}
