<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ParticipantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if user has permission to manage participants
        return $this->user()->can('create participants') || 
               $this->user()->can('edit participants') ||
               $this->user()->can('manage participants');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $participantId = $this->route('participant') ? $this->route('participant')->id : null;
        $workshopId = $this->input('workshop_id') ?? $this->route('workshop_id');

        return [
            'workshop_id' => [
                'required',
                'integer',
                'exists:workshops,id',
            ],
            'ticket_type_id' => [
                'required',
                'integer',
                'exists:ticket_types,id',
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                // Unique email within the same workshop
                Rule::unique('participants', 'email')
                    ->where('workshop_id', $workshopId)
                    ->ignore($participantId),
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[\+]?[0-9\s\-\(\)]+$/',
            ],
            'occupation' => [
                'nullable',
                'string',
                'max:255',
            ],
            'address' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'company' => [
                'nullable',
                'string',
                'max:255',
            ],
            'position' => [
                'nullable',
                'string',
                'max:255',
            ],
            'is_paid' => [
                'sometimes',
                'boolean',
            ],
            'is_checked_in' => [
                'sometimes',
                'boolean',
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
            'ticket_type_id.required' => 'Ticket type is required.',
            'ticket_type_id.exists' => 'Selected ticket type does not exist.',
            'name.required' => 'Participant name is required.',
            'name.max' => 'Participant name cannot exceed 255 characters.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'A participant with this email already exists in this workshop.',
            'email.max' => 'Email address cannot exceed 255 characters.',
            'phone.regex' => 'Please provide a valid phone number.',
            'phone.max' => 'Phone number cannot exceed 20 characters.',
            'occupation.max' => 'Occupation cannot exceed 255 characters.',
            'address.max' => 'Address cannot exceed 1000 characters.',
            'company.max' => 'Company name cannot exceed 255 characters.',
            'position.max' => 'Position cannot exceed 255 characters.',
            'is_paid.boolean' => 'Payment status must be true or false.',
            'is_checked_in.boolean' => 'Check-in status must be true or false.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'workshop_id' => 'workshop',
            'ticket_type_id' => 'ticket type',
            'is_paid' => 'payment status',
            'is_checked_in' => 'check-in status',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Additional business rule validations
            $this->validateTicketTypeWorkshop($validator);
            $this->validateWorkshopStatus($validator);
        });
    }

    /**
     * Validate that ticket type belongs to the selected workshop.
     */
    protected function validateTicketTypeWorkshop($validator): void
    {
        $workshopId = $this->input('workshop_id');
        $ticketTypeId = $this->input('ticket_type_id');

        if ($workshopId && $ticketTypeId) {
            $ticketType = \App\Models\TicketType::where('id', $ticketTypeId)
                ->where('workshop_id', $workshopId)
                ->first();

            if (!$ticketType) {
                $validator->errors()->add('ticket_type_id', 'Selected ticket type does not belong to the selected workshop.');
            }
        }
    }

    /**
     * Validate workshop status for participant registration.
     */
    protected function validateWorkshopStatus($validator): void
    {
        $workshopId = $this->input('workshop_id');

        if ($workshopId) {
            $workshop = \App\Models\Workshop::find($workshopId);

            if ($workshop) {
                // Prevent adding participants to cancelled workshops
                if ($workshop->status === 'cancelled') {
                    $validator->errors()->add('workshop_id', 'Cannot add participants to a cancelled workshop.');
                }

                // Prevent adding participants to completed workshops
                if ($workshop->status === 'completed') {
                    $validator->errors()->add('workshop_id', 'Cannot add participants to a completed workshop.');
                }

                // Warning for workshops that have already started (ongoing)
                if ($workshop->status === 'ongoing' && !$this->route('participant')) {
                    // This is a new participant being added to an ongoing workshop
                    // We'll allow it but could add a warning in the controller
                }
            }
        }
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Normalize email to lowercase
        if ($this->has('email')) {
            $this->merge([
                'email' => strtolower(trim($this->input('email'))),
            ]);
        }

        // Normalize phone number
        if ($this->has('phone') && $this->input('phone')) {
            $phone = preg_replace('/[^\+0-9]/', '', $this->input('phone'));
            $this->merge([
                'phone' => $phone,
            ]);
        }

        // Trim string fields
        $stringFields = ['name', 'occupation', 'address', 'company', 'position'];
        $trimmedData = [];

        foreach ($stringFields as $field) {
            if ($this->has($field) && $this->input($field)) {
                $trimmedData[$field] = trim($this->input($field));
            }
        }

        if (!empty($trimmedData)) {
            $this->merge($trimmedData);
        }

        // Set default values
        if (!$this->has('is_paid')) {
            $this->merge(['is_paid' => false]);
        }

        if (!$this->has('is_checked_in')) {
            $this->merge(['is_checked_in' => false]);
        }
    }

    /**
     * Get validated data with additional processing.
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        // Remove empty optional fields
        $optionalFields = ['phone', 'occupation', 'address', 'company', 'position'];
        
        foreach ($optionalFields as $field) {
            if (isset($validated[$field]) && empty($validated[$field])) {
                $validated[$field] = null;
            }
        }

        return $validated;
    }
}
