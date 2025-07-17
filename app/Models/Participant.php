<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Participant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'workshop_id',
        'ticket_type_id',
        'name',
        'email',
        'phone',
        'occupation',
        'address',
        'company',
        'position',
        'ticket_code',
        'is_paid',
        'is_checked_in',
        'checked_in_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_paid' => 'boolean',
        'is_checked_in' => 'boolean',
        'checked_in_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($participant) {
            if (empty($participant->ticket_code)) {
                $participant->ticket_code = $participant->generateTicketCode();
            }
        });
    }

    /**
     * Get the workshop that owns this participant.
     */
    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }

    /**
     * Get the ticket type for this participant.
     */
    public function ticketType(): BelongsTo
    {
        return $this->belongsTo(TicketType::class);
    }

    /**
     * Generate a unique ticket code.
     */
    public function generateTicketCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (self::where('ticket_code', $code)->exists());

        return $code;
    }

    /**
     * Get the QR code URL for this participant.
     */
    public function getQrCodeUrl(): string
    {
        return route('qr-code.generate', ['ticket_code' => $this->ticket_code]);
    }

    /**
     * Check in the participant.
     */
    public function checkIn(): void
    {
        $this->update([
            'is_checked_in' => true,
            'checked_in_at' => now(),
        ]);
    }

    /**
     * Scope to get paid participants.
     */
    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }

    /**
     * Scope to get unpaid participants.
     */
    public function scopeUnpaid($query)
    {
        return $query->where('is_paid', false);
    }

    /**
     * Scope to get checked-in participants.
     */
    public function scopeCheckedIn($query)
    {
        return $query->where('is_checked_in', true);
    }

    /**
     * Scope to get not checked-in participants.
     */
    public function scopeNotCheckedIn($query)
    {
        return $query->where('is_checked_in', false);
    }

    /**
     * Scope to get participants for a specific workshop.
     */
    public function scopeForWorkshop($query, int $workshopId)
    {
        return $query->where('workshop_id', $workshopId);
    }
}
