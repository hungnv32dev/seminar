<?php

namespace App\Services;

use App\Models\Workshop;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Exception;

class WorkshopService
{
    /**
     * Create a new workshop.
     */
    public function createWorkshop(array $data): Workshop
    {
        DB::beginTransaction();
        
        try {
            $workshop = Workshop::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'location' => $data['location'],
                'status' => $data['status'] ?? 'draft',
                'created_by' => $data['created_by'],
            ]);

            // Attach organizers if provided
            if (isset($data['organizers']) && is_array($data['organizers'])) {
                $workshop->organizers()->attach($data['organizers']);
            }

            DB::commit();
            
            return $workshop->load(['creator', 'organizers']);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing workshop.
     */
    public function updateWorkshop(Workshop $workshop, array $data): Workshop
    {
        DB::beginTransaction();
        
        try {
            $workshop->update([
                'name' => $data['name'] ?? $workshop->name,
                'description' => $data['description'] ?? $workshop->description,
                'start_date' => $data['start_date'] ?? $workshop->start_date,
                'end_date' => $data['end_date'] ?? $workshop->end_date,
                'location' => $data['location'] ?? $workshop->location,
                'status' => $data['status'] ?? $workshop->status,
            ]);

            // Update organizers if provided
            if (isset($data['organizers']) && is_array($data['organizers'])) {
                $workshop->organizers()->sync($data['organizers']);
            }

            DB::commit();
            
            return $workshop->fresh(['creator', 'organizers']);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete a workshop with dependency handling.
     */
    public function deleteWorkshop(Workshop $workshop): bool
    {
        DB::beginTransaction();
        
        try {
            // Check if workshop has participants
            if ($workshop->participants()->count() > 0) {
                throw new Exception('Cannot delete workshop with existing participants. Please remove participants first.');
            }

            // Delete related ticket types (cascade will handle this, but explicit is better)
            $workshop->ticketTypes()->delete();
            
            // Delete related email templates
            $workshop->emailTemplates()->delete();
            
            // Detach organizers
            $workshop->organizers()->detach();
            
            // Delete the workshop
            $deleted = $workshop->delete();

            DB::commit();
            
            return $deleted;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get workshop statistics.
     */
    public function getWorkshopStatistics(Workshop $workshop): array
    {
        $participants = $workshop->participants();
        $totalParticipants = $participants->count();
        $checkedInParticipants = $participants->where('is_checked_in', true)->count();
        $paidParticipants = $participants->where('is_paid', true)->count();

        // Calculate revenue
        $totalRevenue = $workshop->participants()
            ->join('ticket_types', 'participants.ticket_type_id', '=', 'ticket_types.id')
            ->where('participants.is_paid', true)
            ->sum('ticket_types.price');

        // Get ticket type breakdown
        $ticketTypeStats = $workshop->ticketTypes()
            ->withCount(['participants', 'participants as paid_participants_count' => function ($query) {
                $query->where('is_paid', true);
            }, 'participants as checked_in_participants_count' => function ($query) {
                $query->where('is_checked_in', true);
            }])
            ->get()
            ->map(function ($ticketType) {
                return [
                    'id' => $ticketType->id,
                    'name' => $ticketType->name,
                    'price' => $ticketType->price,
                    'total_participants' => $ticketType->participants_count,
                    'paid_participants' => $ticketType->paid_participants_count,
                    'checked_in_participants' => $ticketType->checked_in_participants_count,
                    'revenue' => $ticketType->paid_participants_count * $ticketType->price,
                ];
            });

        return [
            'workshop_id' => $workshop->id,
            'workshop_name' => $workshop->name,
            'workshop_status' => $workshop->status,
            'start_date' => $workshop->start_date,
            'end_date' => $workshop->end_date,
            'location' => $workshop->location,
            'total_participants' => $totalParticipants,
            'checked_in_participants' => $checkedInParticipants,
            'paid_participants' => $paidParticipants,
            'total_revenue' => $totalRevenue,
            'check_in_rate' => $totalParticipants > 0 ? round(($checkedInParticipants / $totalParticipants) * 100, 1) : 0,
            'payment_rate' => $totalParticipants > 0 ? round(($paidParticipants / $totalParticipants) * 100, 1) : 0,
            'ticket_types' => $ticketTypeStats,
            'organizers' => $workshop->organizers->map(function ($organizer) {
                return [
                    'id' => $organizer->id,
                    'name' => $organizer->name,
                    'email' => $organizer->email,
                ];
            }),
        ];
    }

    /**
     * Get all workshops with optional filters.
     */
    public function getWorkshops(array $filters = [])
    {
        $query = Workshop::with(['creator', 'organizers', 'participants', 'ticketTypes']);

        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['created_by'])) {
            $query->where('created_by', $filters['created_by']);
        }

        if (isset($filters['organizer_id'])) {
            $query->whereHas('organizers', function ($q) use ($filters) {
                $q->where('users.id', $filters['organizer_id']);
            });
        }

        if (isset($filters['date_from'])) {
            $query->where('start_date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('end_date', '<=', $filters['date_to']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('location', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate(15)->appends(request()->query());
    }

    /**
     * Get workshops for a specific user (created or organized).
     */
    public function getUserWorkshops(User $user): Collection
    {
        return Workshop::where(function ($query) use ($user) {
            $query->where('created_by', $user->id)
                  ->orWhereHas('organizers', function ($q) use ($user) {
                      $q->where('users.id', $user->id);
                  });
        })
        ->with(['creator', 'organizers', 'participants', 'ticketTypes'])
        ->orderBy('created_at', 'desc')
        ->get();
    }

    /**
     * Duplicate a workshop.
     */
    public function duplicateWorkshop(Workshop $originalWorkshop, array $overrides = []): Workshop
    {
        DB::beginTransaction();
        
        try {
            // Create new workshop with original data
            $newWorkshopData = [
                'name' => $overrides['name'] ?? $originalWorkshop->name . ' (Copy)',
                'description' => $overrides['description'] ?? $originalWorkshop->description,
                'start_date' => $overrides['start_date'] ?? $originalWorkshop->start_date,
                'end_date' => $overrides['end_date'] ?? $originalWorkshop->end_date,
                'location' => $overrides['location'] ?? $originalWorkshop->location,
                'status' => $overrides['status'] ?? 'draft',
                'created_by' => $overrides['created_by'] ?? $originalWorkshop->created_by,
            ];

            $newWorkshop = Workshop::create($newWorkshopData);

            // Copy organizers
            $organizerIds = $originalWorkshop->organizers->pluck('id')->toArray();
            if (!empty($organizerIds)) {
                $newWorkshop->organizers()->attach($organizerIds);
            }

            // Copy ticket types
            foreach ($originalWorkshop->ticketTypes as $ticketType) {
                $newWorkshop->ticketTypes()->create([
                    'name' => $ticketType->name,
                    'price' => $ticketType->price,
                ]);
            }

            // Copy email templates
            foreach ($originalWorkshop->emailTemplates as $template) {
                $newWorkshop->emailTemplates()->create([
                    'type' => $template->type,
                    'subject' => $template->subject,
                    'content' => $template->content,
                ]);
            }

            DB::commit();
            
            return $newWorkshop->load(['creator', 'organizers', 'ticketTypes', 'emailTemplates']);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update workshop status.
     */
    public function updateWorkshopStatus(Workshop $workshop, string $status): Workshop
    {
        $validStatuses = ['draft', 'published', 'ongoing', 'completed', 'cancelled'];
        
        if (!in_array($status, $validStatuses)) {
            throw new Exception("Invalid status. Valid statuses are: " . implode(', ', $validStatuses));
        }

        $workshop->update(['status' => $status]);
        
        return $workshop->fresh();
    }
}