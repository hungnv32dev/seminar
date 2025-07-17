<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'workshop_id',
        'name',
        'price',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Get the workshop that owns this ticket type.
     */
    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }

    /**
     * Get the participants for this ticket type.
     */
    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    /**
     * Get the count of participants for this ticket type.
     */
    public function getParticipantCountAttribute(): int
    {
        return $this->participants()->count();
    }

    /**
     * Get the total revenue for this ticket type.
     */
    public function getTotalRevenueAttribute(): float
    {
        return $this->participants()->where('is_paid', true)->count() * $this->price;
    }

    /**
     * Scope to get ticket types for a specific workshop.
     */
    public function scopeForWorkshop($query, int $workshopId)
    {
        return $query->where('workshop_id', $workshopId);
    }
}
