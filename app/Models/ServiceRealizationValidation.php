<?php

namespace App\Models;

use App\Enums\ValidationResult;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRealizationValidation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'service_realization_id',
        'validated_by',
        'result',
        'notes',
        'validated_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'result' => ValidationResult::class,
            'validated_at' => 'datetime',
        ];
    }

    /**
     * Check if validation result is valid.
     */
    public function isValid(): bool
    {
        return $this->result === ValidationResult::Valid;
    }

    /**
     * Check if validation result requires revision.
     */
    public function needsRevision(): bool
    {
        return $this->result === ValidationResult::NeedsRevision;
    }

    /**
     * Service realization being validated.
     */
    public function serviceRealization(): BelongsTo
    {
        return $this->belongsTo(ServiceRealization::class);
    }

    /**
     * Coordinator/supervisor who validated this realization.
     */
    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}
