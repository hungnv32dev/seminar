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

        // Ensure all variables are strings to prevent errors
        $variables = array_map(function ($value) {
            if (is_null($value)) {
                return '';
            }
            if (is_bool($value)) {
                return $value ? 'Yes' : 'No';
            }
            if (is_array($value) || is_object($value)) {
                return json_encode($value);
            }
            return (string) $value;
        }, $variables);

        // Replace variables in both subject and content
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
     * Validate template syntax and variables.
     */
    public function validateTemplate(): array
    {
        $errors = [];
        $availableVariables = array_keys(self::getAvailableVariables());
        
        // Find all variables used in subject and content
        $usedVariables = [];
        preg_match_all('/\{\{\s*(\w+)\s*\}\}/', $this->subject . ' ' . $this->content, $matches);
        
        if (!empty($matches[1])) {
            $usedVariables = array_unique($matches[1]);
        }

        // Check for undefined variables
        foreach ($usedVariables as $variable) {
            if (!in_array($variable, $availableVariables)) {
                $errors[] = "Unknown variable: {{ {$variable} }}";
            }
        }

        // Check for basic HTML validity in content (if it contains HTML)
        if (strip_tags($this->content) !== $this->content) {
            // Contains HTML, do basic validation
            if (substr_count($this->content, '<') !== substr_count($this->content, '>')) {
                $errors[] = "HTML tags appear to be unbalanced in content";
            }
        }

        return $errors;
    }

    /**
     * Get unused variables in the template.
     */
    public function getUnusedVariables(): array
    {
        $availableVariables = array_keys(self::getAvailableVariables());
        
        // Find all variables used in subject and content
        $usedVariables = [];
        preg_match_all('/\{\{\s*(\w+)\s*\}\}/', $this->subject . ' ' . $this->content, $matches);
        
        if (!empty($matches[1])) {
            $usedVariables = array_unique($matches[1]);
        }

        return array_diff($availableVariables, $usedVariables);
    }

    /**
     * Preview the template with sample data.
     */
    public function preview(): array
    {
        $sampleVariables = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'phone' => '+1 (555) 123-4567',
            'company' => 'Example Corp',
            'position' => 'Software Developer',
            'occupation' => 'Developer',
            'address' => '123 Main St, City, State 12345',
            'ticket_code' => 'WS2024-ABC123',
            'qr_code_url' => 'https://example.com/qr/WS2024-ABC123',
            'is_paid' => 'Yes',
            'payment_status' => 'Paid',
            'check_in_status' => 'Not Checked In',
            'registration_date' => 'January 15, 2024',
            'workshop_name' => 'Sample Workshop',
            'workshop_description' => 'This is a sample workshop description.',
            'workshop_location' => 'Conference Room A, Main Building',
            'workshop_start_date' => '2024-02-15 09:00:00',
            'workshop_end_date' => '2024-02-15 17:00:00',
            'workshop_start_date_formatted' => 'February 15, 2024 at 9:00 AM',
            'workshop_end_date_formatted' => 'February 15, 2024 at 5:00 PM',
            'workshop_date_range' => 'February 15, 2024',
            'ticket_type_name' => 'Standard Ticket',
            'ticket_type_price' => '99.00',
            'ticket_type_price_formatted' => '$99.00',
            'app_name' => 'Workshop Management System',
            'app_url' => 'https://example.com',
            'days_until_workshop' => '30',
        ];

        return $this->render($sampleVariables);
    }

    /**
     * Get the available variables for templates.
     */
    public static function getAvailableVariables(): array
    {
        return [
            // Participant Information
            'name' => 'Participant Name',
            'email' => 'Participant Email',
            'phone' => 'Participant Phone',
            'company' => 'Participant Company',
            'position' => 'Participant Position',
            'occupation' => 'Participant Occupation',
            'address' => 'Participant Address',
            
            // Ticket Information
            'ticket_code' => 'Ticket Code',
            'qr_code_url' => 'QR Code URL',
            'is_paid' => 'Payment Status (Yes/No)',
            'payment_status' => 'Payment Status (Paid/Unpaid)',
            'check_in_status' => 'Check-in Status',
            'registration_date' => 'Registration Date',
            
            // Workshop Information
            'workshop_name' => 'Workshop Name',
            'workshop_description' => 'Workshop Description',
            'workshop_location' => 'Workshop Location',
            'workshop_start_date' => 'Workshop Start Date (Raw)',
            'workshop_end_date' => 'Workshop End Date (Raw)',
            'workshop_start_date_formatted' => 'Workshop Start Date (Formatted)',
            'workshop_end_date_formatted' => 'Workshop End Date (Formatted)',
            'workshop_date_range' => 'Workshop Date Range',
            
            // Ticket Type Information
            'ticket_type_name' => 'Ticket Type Name',
            'ticket_type_price' => 'Ticket Type Price (Raw)',
            'ticket_type_price_formatted' => 'Ticket Type Price (Formatted)',
            
            // System Information
            'app_name' => 'Application Name',
            'app_url' => 'Application URL',
            'days_until_workshop' => 'Days Until Workshop',
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
