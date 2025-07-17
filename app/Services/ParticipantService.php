<?php

namespace App\Services;

use App\Models\Participant;
use App\Models\Workshop;
use App\Models\TicketType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

class ParticipantService
{
    /**
     * Create a new participant.
     */
    public function createParticipant(array $data): Participant
    {
        DB::beginTransaction();
        
        try {
            // Check if email already exists in the workshop
            $existingParticipant = Participant::where('workshop_id', $data['workshop_id'])
                ->where('email', $data['email'])
                ->first();

            if ($existingParticipant) {
                throw new Exception('A participant with this email already exists in this workshop.');
            }

            // Verify ticket type belongs to the workshop
            $ticketType = TicketType::where('id', $data['ticket_type_id'])
                ->where('workshop_id', $data['workshop_id'])
                ->first();

            if (!$ticketType) {
                throw new Exception('Invalid ticket type for this workshop.');
            }

            $participant = Participant::create([
                'workshop_id' => $data['workshop_id'],
                'ticket_type_id' => $data['ticket_type_id'],
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'occupation' => $data['occupation'] ?? null,
                'address' => $data['address'] ?? null,
                'company' => $data['company'] ?? null,
                'position' => $data['position'] ?? null,
                'is_paid' => $data['is_paid'] ?? false,
                'is_checked_in' => false,
            ]);

            DB::commit();
            
            return $participant->load(['workshop', 'ticketType']);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing participant.
     */
    public function updateParticipant(Participant $participant, array $data): Participant
    {
        DB::beginTransaction();
        
        try {
            // Check email uniqueness if email is being changed
            if (isset($data['email']) && $data['email'] !== $participant->email) {
                $existingParticipant = Participant::where('workshop_id', $participant->workshop_id)
                    ->where('email', $data['email'])
                    ->where('id', '!=', $participant->id)
                    ->first();

                if ($existingParticipant) {
                    throw new Exception('A participant with this email already exists in this workshop.');
                }
            }

            // Verify ticket type belongs to the workshop if being changed
            if (isset($data['ticket_type_id']) && $data['ticket_type_id'] !== $participant->ticket_type_id) {
                $ticketType = TicketType::where('id', $data['ticket_type_id'])
                    ->where('workshop_id', $participant->workshop_id)
                    ->first();

                if (!$ticketType) {
                    throw new Exception('Invalid ticket type for this workshop.');
                }
            }

            $participant->update([
                'name' => $data['name'] ?? $participant->name,
                'email' => $data['email'] ?? $participant->email,
                'phone' => $data['phone'] ?? $participant->phone,
                'occupation' => $data['occupation'] ?? $participant->occupation,
                'address' => $data['address'] ?? $participant->address,
                'company' => $data['company'] ?? $participant->company,
                'position' => $data['position'] ?? $participant->position,
                'ticket_type_id' => $data['ticket_type_id'] ?? $participant->ticket_type_id,
                'is_paid' => $data['is_paid'] ?? $participant->is_paid,
            ]);

            DB::commit();
            
            return $participant->fresh(['workshop', 'ticketType']);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete a participant.
     */
    public function deleteParticipant(Participant $participant): bool
    {
        return $participant->delete();
    }

    /**
     * Import participants from Excel file.
     */
    public function importFromExcel(UploadedFile $file, Workshop $workshop, TicketType $defaultTicketType = null): array
    {
        DB::beginTransaction();
        
        try {
            $data = Excel::toArray([], $file);
            
            if (empty($data) || empty($data[0])) {
                throw new Exception('Excel file is empty or invalid.');
            }

            $rows = $data[0];
            $header = array_shift($rows); // Remove header row
            
            // Map header to expected columns
            $headerMap = $this->mapExcelHeaders($header);
            
            $imported = [];
            $errors = [];
            $skipped = [];

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2; // +2 because we removed header and Excel starts from 1
                
                try {
                    // Skip empty rows
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    $participantData = $this->mapRowData($row, $headerMap);
                    
                    // Validate required fields
                    if (empty($participantData['name']) || empty($participantData['email'])) {
                        $errors[] = "Row {$rowNumber}: Name and Email are required.";
                        continue;
                    }

                    // Check if participant already exists
                    $existingParticipant = Participant::where('workshop_id', $workshop->id)
                        ->where('email', $participantData['email'])
                        ->first();

                    if ($existingParticipant) {
                        $skipped[] = "Row {$rowNumber}: Participant with email {$participantData['email']} already exists.";
                        continue;
                    }

                    // Determine ticket type
                    $ticketType = $defaultTicketType;
                    if (!empty($participantData['ticket_type_name'])) {
                        $ticketType = $workshop->ticketTypes()
                            ->where('name', 'like', '%' . $participantData['ticket_type_name'] . '%')
                            ->first() ?? $defaultTicketType;
                    }

                    if (!$ticketType) {
                        $errors[] = "Row {$rowNumber}: No valid ticket type found.";
                        continue;
                    }

                    // Create participant
                    $participant = Participant::create([
                        'workshop_id' => $workshop->id,
                        'ticket_type_id' => $ticketType->id,
                        'name' => $participantData['name'],
                        'email' => $participantData['email'],
                        'phone' => $participantData['phone'],
                        'occupation' => $participantData['occupation'],
                        'address' => $participantData['address'],
                        'company' => $participantData['company'],
                        'position' => $participantData['position'],
                        'is_paid' => $participantData['is_paid'] ?? false,
                    ]);

                    $imported[] = $participant;

                } catch (Exception $e) {
                    $errors[] = "Row {$rowNumber}: " . $e->getMessage();
                }
            }

            DB::commit();

            return [
                'imported' => $imported,
                'errors' => $errors,
                'skipped' => $skipped,
                'total_processed' => count($rows),
                'total_imported' => count($imported),
                'total_errors' => count($errors),
                'total_skipped' => count($skipped),
            ];

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Generate a unique ticket code.
     */
    public function generateTicketCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (Participant::where('ticket_code', $code)->exists());

        return $code;
    }

    /**
     * Check in a participant using ticket code.
     */
    public function checkIn(string $ticketCode): Participant
    {
        $participant = Participant::where('ticket_code', $ticketCode)->first();

        if (!$participant) {
            throw new Exception('Invalid ticket code.');
        }

        if ($participant->is_checked_in) {
            throw new Exception('Participant is already checked in at ' . $participant->checked_in_at->format('Y-m-d H:i:s'));
        }

        $participant->checkIn();

        return $participant->fresh(['workshop', 'ticketType']);
    }

    /**
     * Get participants with optional filters.
     */
    public function getParticipants(array $filters = []): Collection
    {
        $query = Participant::with(['workshop', 'ticketType']);

        // Apply filters
        if (isset($filters['workshop_id'])) {
            $query->where('workshop_id', $filters['workshop_id']);
        }

        if (isset($filters['ticket_type_id'])) {
            $query->where('ticket_type_id', $filters['ticket_type_id']);
        }

        if (isset($filters['is_paid'])) {
            $query->where('is_paid', $filters['is_paid']);
        }

        if (isset($filters['is_checked_in'])) {
            $query->where('is_checked_in', $filters['is_checked_in']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('company', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('ticket_code', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->get();
    }

    /**
     * Update payment status for multiple participants.
     */
    public function updatePaymentStatus(array $participantIds, bool $isPaid): int
    {
        return Participant::whereIn('id', $participantIds)
            ->update(['is_paid' => $isPaid]);
    }

    /**
     * Get participant statistics for a workshop.
     */
    public function getWorkshopParticipantStats(Workshop $workshop): array
    {
        $totalParticipants = $workshop->participants()->count();
        $paidParticipants = $workshop->participants()->where('is_paid', true)->count();
        $unpaidParticipants = $workshop->participants()->where('is_paid', false)->count();
        $checkedInParticipants = $workshop->participants()->where('is_checked_in', true)->count();
        $notCheckedInParticipants = $workshop->participants()->where('is_checked_in', false)->count();
        
        return [
            'total' => $totalParticipants,
            'paid' => $paidParticipants,
            'unpaid' => $unpaidParticipants,
            'checked_in' => $checkedInParticipants,
            'not_checked_in' => $notCheckedInParticipants,
            'by_ticket_type' => $workshop->ticketTypes()
                ->withCount(['participants', 'participants as paid_count' => function ($query) {
                    $query->where('is_paid', true);
                }, 'participants as checked_in_count' => function ($query) {
                    $query->where('is_checked_in', true);
                }])
                ->get()
                ->map(function ($ticketType) {
                    return [
                        'ticket_type' => $ticketType->name,
                        'price' => $ticketType->price,
                        'total' => $ticketType->participants_count,
                        'paid' => $ticketType->paid_count,
                        'checked_in' => $ticketType->checked_in_count,
                        'revenue' => $ticketType->paid_count * $ticketType->price,
                    ];
                }),
        ];
    }

    /**
     * Map Excel headers to expected column names.
     */
    private function mapExcelHeaders(array $headers): array
    {
        $map = [];
        
        foreach ($headers as $index => $header) {
            $header = strtolower(trim($header));
            
            // Map common header variations
            if (in_array($header, ['name', 'full name', 'participant name', 'họ tên'])) {
                $map['name'] = $index;
            } elseif (in_array($header, ['email', 'email address', 'e-mail'])) {
                $map['email'] = $index;
            } elseif (in_array($header, ['phone', 'phone number', 'mobile', 'số điện thoại'])) {
                $map['phone'] = $index;
            } elseif (in_array($header, ['occupation', 'job', 'nghề nghiệp'])) {
                $map['occupation'] = $index;
            } elseif (in_array($header, ['address', 'địa chỉ'])) {
                $map['address'] = $index;
            } elseif (in_array($header, ['company', 'organization', 'công ty'])) {
                $map['company'] = $index;
            } elseif (in_array($header, ['position', 'title', 'chức vụ'])) {
                $map['position'] = $index;
            } elseif (in_array($header, ['ticket type', 'ticket_type', 'loại vé'])) {
                $map['ticket_type_name'] = $index;
            } elseif (in_array($header, ['paid', 'is_paid', 'payment status', 'đã thanh toán'])) {
                $map['is_paid'] = $index;
            }
        }
        
        return $map;
    }

    /**
     * Map row data using header mapping.
     */
    private function mapRowData(array $row, array $headerMap): array
    {
        $data = [];
        
        foreach ($headerMap as $field => $index) {
            $value = isset($row[$index]) ? trim($row[$index]) : null;
            
            // Special handling for boolean fields
            if ($field === 'is_paid') {
                $data[$field] = in_array(strtolower($value), ['yes', 'true', '1', 'paid', 'có', 'đã thanh toán']);
            } else {
                $data[$field] = $value;
            }
        }
        
        return $data;
    }
}