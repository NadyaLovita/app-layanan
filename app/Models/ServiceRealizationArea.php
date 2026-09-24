<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRealizationArea extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'service_realization_id',
        'service_area_id',
        'location_id',
        'sequence',
        'arrived_at',
        'volume',
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
            'sequence' => 'integer',
            'arrived_at' => 'datetime',
            'volume' => 'decimal:2',
        ];
    }

    /**
     * Service realization this stop belongs to.
     */
    public function serviceRealization(): BelongsTo
    {
        return $this->belongsTo(ServiceRealization::class);
    }

    /**
     * Service area visited.
     */
    public function serviceArea(): BelongsTo
    {
        return $this->belongsTo(ServiceArea::class);
    }

    /**
     * Specific location visited, if any.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
