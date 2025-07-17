<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WorkshopRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if user has permission to create or edit workshops
        return $this->user()->can('create workshops') || $this->user()->can('edit workshops');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $workshopId = $this->route('workshop') ? $this->route('workshop')->id : null;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('workshops', 'name')->ignore($workshopId),
            ],
            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],
            'start_date' => [
                'required',
                'date',
                'after:now',
            ],
            'end_date' => [
                'required',
                'date',
                'after:start_date',
            ],
            'location' => [
                'required',
                'string',
                'max:255',
            ],
            'status' => [
                'sometimes',
                'string',
                Rule::in(['draft', 'published', 'ongoing', 'completed', 'cancelled']),
            ],
            'organizers' => [
                'sometimes',
                'array',
            ],
            'organizers.*' => [
                'integer',
                'exists:users,id',
            ],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Workshop name is required.',
            'name.unique' => 'A workshop with this name already exists.',
            'name.max' => 'Workshop name cannot exceed 255 characters.',
            'description.max' => 'Description cannot exceed 5000 characters.',
            'start_date.required' => 'Start date is required.',
            'start_date.date' => 'Start date must be a valid date.',
            'start_date.after' => 'Start date must be in the future.',
            'end_date.required' => 'End date is required.',
            'end_date.date' => 'End date must be a valid date.',
            'end_date.after' => 'End date must be after the start date.',
            'location.required' => 'Location is required.',
            'location.max' => 'Location cannot exceed 255 characters.',
            'status.in' => 'Invalid workshop status.',
            'organizers.array' => 'Organizers must be an array.',
            'organizers.*.integer' => 'Each organizer ID must be an integer.',
            'organizers.*.exists' => 'One or more selected organizers do not exist.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'workshop name',
            'start_date' => 'start date',
            'end_date' => 'end date',
            'organizers.*' => 'organizer',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Additional business rule validations
            $this->validateWorkshopDates($validator);
            $this->validateWorkshopStatus($validator);
        });
    }

    /**
     * Validate workshop dates business rules.
     */
    protected function validateWorkshopDates($validator): void
    {
        $startDate = $this->input('start_date');
        $endDate = $this->input('end_date');

        if ($startDate && $endDate) {
            $start = \Carbon\Carbon::parse($startDate);
            $end = \Carbon\Carbon::parse($endDate);

            // Check if workshop duration is reasonable (not more than 30 days)
            if ($start->diffInDays($end) > 30) {
                $validator->errors()->add('end_date', 'Workshop duration cannot exceed 30 days.');
            }

            // Check if workshop is not too far in the future (not more than 2 years)
            if ($start->diffInYears(now()) > 2) {
                $validator->errors()->add('start_date', 'Workshop cannot be scheduled more than 2 years in advance.');
            }
        }
    }

    /**
     * Validate workshop status business rules.
     */
    protected function validateWorkshopStatus($validator): void
    {
        $status = $this->input('status');
        $workshopId = $this->route('workshop') ? $this->route('workshop')->id : null;

        if ($status && $workshopId) {
            $workshop = \App\Models\Workshop::find($workshopId);
            
            if ($workshop) {
                // Prevent changing status from completed or cancelled to other statuses
                if (in_array($workshop->status, ['completed', 'cancelled']) && 
                    !in_array($status, ['completed', 'cancelled'])) {
                    $validator->errors()->add('status', 'Cannot change status from ' . $workshop->status . ' to ' . $status . '.');
                }

                // Prevent setting status to ongoing if start date is in the future
                if ($status === 'ongoing') {
                    $startDate = $this->input('start_date') ? 
                        \Carbon\Carbon::parse($this->input('start_date')) : 
                        $workshop->start_date;
                    
                    if ($startDate->isFuture()) {
                        $validator->errors()->add('status', 'Cannot set status to ongoing for future workshops.');
                    }
                }

                // Prevent setting status to completed if end date is in the future
                if ($status === 'completed') {
                    $endDate = $this->input('end_date') ? 
                        \Carbon\Carbon::parse($this->input('end_date')) : 
                        $workshop->end_date;
                    
                    if ($endDate->isFuture()) {
                        $validator->errors()->add('status', 'Cannot set status to completed for workshops that have not ended yet.');
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
        // Set created_by to current user if not provided
        if (!$this->has('created_by')) {
            $this->merge([
                'created_by' => $this->user()->id,
            ]);
        }

        // Set default status if not provided
        if (!$this->has('status')) {
            $this->merge([
                'status' => 'draft',
            ]);
        }
    }
}
