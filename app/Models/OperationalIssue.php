<?php

namespace App\Models;

use App\Enums\FollowUpStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OperationalIssue extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'service_realization_id',
        'issue_type_id',
        'description',
        'occurred_at',
        'follow_up',
        'follow_up_status',
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
            'occurred_at' => 'datetime',
            'follow_up_status' => FollowUpStatus::class,
        ];
    }

    /**
     * Scope query for open issues.
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('follow_up_status', FollowUpStatus::Open->value);
    }

    /**
     * Scope query for issues in progress.
     */
    public function scopeInProgress(Builder $query): Builder
    {
        return $query->where('follow_up_status', FollowUpStatus::InProgress->value);
    }

    /**
     * Scope query for resolved issues.
     */
    public function scopeDone(Builder $query): Builder
    {
        return $query->where('follow_up_status', FollowUpStatus::Done->value);
    }

    /**
     * Service realization during which this issue occurred.
     */
    public function serviceRealization(): BelongsTo
    {
        return $this->belongsTo(ServiceRealization::class);
    }

    /**
     * Type/category of issue.
     */
    public function issueType(): BelongsTo
    {
        return $this->belongsTo(IssueType::class);
    }

    /**
     * User who logged this issue.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Photos or files documenting this issue.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }
}
