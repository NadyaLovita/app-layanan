<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OperationPlan extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'plan_date',
        'vehicle_id',
        'driver_id',
        'notes',
        'created_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'plan_date' => 'date',
        ];
    }

    /**
     * Scope a query for a specific plan date.
     */
    public function scopeForDate(Builder $query, CarbonInterface|string $date): Builder
    {
        return $query->whereDate('plan_date', $date);
    }

    /**
     * Scope a query for today's plan.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('plan_date', today());
    }

    /**
     * Vehicle scheduled for this plan.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Driver assigned to this plan.
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * User who created this operational plan.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Planned service areas with sequence numbers.
     */
    public function planAreas(): HasMany
    {
        return $this->hasMany(OperationPlanArea::class)->orderBy('sequence');
    }

    /**
     * Service areas included in this plan.
     */
    public function serviceAreas(): BelongsToMany
    {
        return $this->belongsToMany(ServiceArea::class, 'operation_plan_areas')
            ->withPivot('sequence')
            ->withTimestamps()
            ->orderByPivot('sequence');
    }

    /**
     * Realizations executed against this operational plan.
     */
    public function serviceRealizations(): HasMany
    {
        return $this->hasMany(ServiceRealization::class);
    }
}
