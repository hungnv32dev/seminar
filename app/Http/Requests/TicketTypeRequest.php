<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TicketTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if user has permission to manage ticket types
        return $this->user()->can('create ticket types') || 
               $this->user()->can('edit ticket types') ||
               $this->user()->can('manage ticket types');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $ticketTypeId = $this->route('ticket_type') ? $this->route('ticket_type')->id : null;
        $workshopId = $this->input('workshop_id') ?? $this->route('workshop_id');

        return [
            'workshop_id' => [
                'required',
                'integer',
                'exists:workshops,id',
            ],
            'name' => [
                'required',
                'string',
                'max:255',
                // Unique name within the same workshop
                Rule::unique('ticket_types', 'name')
                    ->where('workshop_id', $workshopId)
                    ->ignore($ticketTypeId),
            ],
            'price' => [
                'required',
                'numeric',
                'min:0',
                'max:999999.99',
                'regex:/^\d+(\.\d{1,2})?$/', // Allow up to 2 decimal places
            ],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'workshop_id.required' => 'Workshop is required.',
            'workshop_id.exists' => 'Selected workshop does not exist.',
            'name.required' => 'Ticket type name is required.',
            'name.max' => 'Ticket type name cannot exceed 255 characters.',
            'name.unique' => 'A ticket type with this name already exists in this workshop.',
            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a valid number.',
            'price.min' => 'Price cannot be negative.',
            'price.max' => 'Price cannot exceed $999,999.99.',
            'price.regex' => 'Price can have at most 2 decimal places.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'workshop_id' => 'workshop',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Additional business rule validations
            $this->validateWorkshopStatus($validator);
            $this->validateTicketTypeDeletion($validator);
        });
    }

    /**
     * Validate workshop status for ticket type management.
     */
    protected function validateWorkshopStatus($validator): void
    {
        $workshopId = $this->input('workshop_id');

        if ($workshopId) {
            $workshop = \App\Models\Workshop::find($workshopId);

            if ($workshop) {
                // Prevent adding/editing ticket types for cancelled workshops
                if ($workshop->status === 'cancelled') {
                    $validator->errors()->add('workshop_id', 'Cannot manage ticket types for a cancelled workshop.');
                }

                // Prevent adding/editing ticket types for completed workshops
                if ($workshop->status === 'completed') {
                    $validator->errors()->add('workshop_id', 'Cannot manage ticket types for a completed workshop.');
                }

                // Warning for ongoing workshops
                if ($workshop->status === 'ongoing' && $this->isMethod('POST')) {
                    // Allow editing existing ticket types but warn about new ones
                    if (!$this->route('ticket_type')) {
                        $validator->errors()->add('workshop_id', 'Adding new ticket types to ongoing workshops may affect existing participants.');
                    }
                }
            }
        }
    }

    /**
     * Validate ticket type deletion constraints.
     */
    protected function validateTicketTypeDeletion($validator): void
    {
        // This validation is primarily for deletion, but we can check if this is an update
        // that might affect existing participants
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $ticketType = $this->route('ticket_type');
            
            if ($ticketType) {
                $participantCount = $ticketType->participants()->count();
                
                // If there are participants and price is being changed significantly
                $currentPrice = $ticketType->price;
                $newPrice = $this->input('price');
                
                if ($participantCount > 0 && $newPrice != $currentPrice) {
                    $priceDifference = abs($newPrice - $currentPrice);
                    $percentageChange = ($priceDifference / $currentPrice) * 100;
                    
                    // Warn if price change is more than 20%
                    if ($percentageChange > 20) {
                        $validator->errors()->add('price', 
                            "Significant price change detected. This ticket type has {$participantCount} participants. " .
                            "Consider creating a new ticket type instead."
                        );
                    }
                }
            }
        }
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Trim the name field
        if ($this->has('name')) {
            $this->merge([
                'name' => trim($this->input('name')),
            ]);
        }

        // Format price to ensure proper decimal places
        if ($this->has('price')) {
            $price = $this->input('price');
            
            // Remove any non-numeric characters except decimal point
            $price = preg_replace('/[^0-9.]/', '', $price);
            
            // Ensure only one decimal point
            $parts = explode('.', $price);
            if (count($parts) > 2) {
                $price = $parts[0] . '.' . $parts[1];
            }
            
            // Round to 2 decimal places
            $price = number_format((float)$price, 2, '.', '');
            
            $this->merge([
                'price' => $price,
            ]);
        }
    }

    /**
     * Get validated data with additional processing.
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        // Ensure price is stored as decimal with 2 places
        if (isset($validated['price'])) {
            $validated['price'] = number_format((float)$validated['price'], 2, '.', '');
        }

        return $validated;
    }

    /**
     * Additional validation for ticket type deletion.
     */
    public function validateForDeletion(): array
    {
        $ticketType = $this->route('ticket_type');
        $errors = [];

        if (!$ticketType) {
            $errors[] = 'Ticket type not found.';
            return $errors;
        }

        // Check if ticket type has participants
        $participantCount = $ticketType->participants()->count();
        
        if ($participantCount > 0) {
            $errors[] = "Cannot delete ticket type '{$ticketType->name}' because it has {$participantCount} participants assigned to it.";
        }

        // Check if this is the only ticket type for the workshop
        $workshopTicketTypeCount = $ticketType->workshop->ticketTypes()->count();
        
        if ($workshopTicketTypeCount <= 1) {
            $errors[] = "Cannot delete the only ticket type for this workshop. Please add another ticket type first.";
        }

        return $errors;
    }
}
