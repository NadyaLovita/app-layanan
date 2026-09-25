<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Determine if the user can access the given Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active ?? true;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'phone',
        'role',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    /**
     * Check if user has Admin role.
     */
    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    /**
     * Check if user has Officer role.
     */
    public function isOfficer(): bool
    {
        return $this->role === UserRole::Officer;
    }

    /**
     * Check if user has Driver role.
     */
    public function isDriver(): bool
    {
        return $this->role === UserRole::Driver;
    }

    /**
     * Check if user has Coordinator role.
     */
    public function isCoordinator(): bool
    {
        return $this->role === UserRole::Coordinator;
    }

    /**
     * Check if user has Head role.
     */
    public function isHead(): bool
    {
        return $this->role === UserRole::Head;
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by role.
     */
    public function scopeRole(Builder $query, UserRole|string $role): Builder
    {
        $roleValue = $role instanceof UserRole ? $role->value : $role;

        return $query->where('role', $roleValue);
    }

    /**
     * Driver profile associated with this user.
     */
    public function driver(): HasOne
    {
        return $this->hasOne(Driver::class);
    }

    /**
     * Operation plans created by this user.
     */
    public function createdOperationPlans(): HasMany
    {
        return $this->hasMany(OperationPlan::class, 'created_by');
    }

    /**
     * Service realizations created by this user.
     */
    public function createdServiceRealizations(): HasMany
    {
        return $this->hasMany(ServiceRealization::class, 'created_by');
    }

    /**
     * Service realizations last updated by this user.
     */
    public function updatedServiceRealizations(): HasMany
    {
        return $this->hasMany(ServiceRealization::class, 'updated_by');
    }

    /**
     * Validations performed by this user (coordinator).
     */
    public function validations(): HasMany
    {
        return $this->hasMany(ServiceRealizationValidation::class, 'validated_by');
    }

    /**
     * Attachments uploaded by this user.
     */
    public function uploadedAttachments(): HasMany
    {
        return $this->hasMany(Attachment::class, 'uploaded_by');
    }

    /**
     * Operational issues reported by this user.
     */
    public function createdIssues(): HasMany
    {
        return $this->hasMany(OperationalIssue::class, 'created_by');
    }

    /**
     * Audit logs caused by this user.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }
}
