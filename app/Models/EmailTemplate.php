<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailTemplate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'workshop_id',
        'type',
        'subject',
        'content',
    ];

    /**
     * The available email template types.
     */
    const TYPES = [
        'invite' => 'Invitation Email',
        'confirm' => 'Confirmation Email',
        'ticket' => 'Ticket Email',
        'reminder' => 'Reminder Email',
        'thank_you' => 'Thank You Email',
    ];

    /**
     * Get the workshop that owns this email template.
     */
    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }

    /**
     * Render the template with variables.
     */
    public function render(array $variables = []): array
    {
        $subject = $this->subject;
        $content = $this->content;

        foreach ($variables as $key => $value) {
            $placeholder = '{{ ' . $key . ' }}';
            $subject = str_replace($placeholder, $value, $subject);
            $content = str_replace($placeholder, $value, $content);
        }

        return [
            'subject' => $subject,
            'content' => $content,
        ];
    }

    /**
     * Get the available variables for templates.
     */
    public static function getAvailableVariables(): array
    {
        return [
            'name' => 'Participant Name',
            'email' => 'Participant Email',
            'phone' => 'Participant Phone',
            'company' => 'Participant Company',
            'position' => 'Participant Position',
            'ticket_code' => 'Ticket Code',
            'qr_code_url' => 'QR Code URL',
            'workshop_name' => 'Workshop Name',
            'workshop_location' => 'Workshop Location',
            'workshop_start_date' => 'Workshop Start Date',
            'workshop_end_date' => 'Workshop End Date',
            'ticket_type_name' => 'Ticket Type Name',
            'ticket_type_price' => 'Ticket Type Price',
        ];
    }

    /**
     * Get the type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    /**
     * Scope to get templates by type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get templates for a specific workshop.
     */
    public function scopeForWorkshop($query, int $workshopId)
    {
        return $query->where('workshop_id', $workshopId);
    }
}
