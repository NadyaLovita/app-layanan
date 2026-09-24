<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attachment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'service_realization_id',
        'operational_issue_id',
        'file_path',
        'caption',
        'uploaded_by',
    ];

    /**
     * Service realization associated with this attachment.
     */
    public function serviceRealization(): BelongsTo
    {
        return $this->belongsTo(ServiceRealization::class);
    }

    /**
     * Operational issue associated with this attachment, if any.
     */
    public function operationalIssue(): BelongsTo
    {
        return $this->belongsTo(OperationalIssue::class);
    }

    /**
     * User who uploaded this attachment.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
