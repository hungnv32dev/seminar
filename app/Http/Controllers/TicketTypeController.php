<?php

namespace App\Http\Controllers;

use App\Models\TicketType;
use App\Models\Workshop;
use App\Http\Requests\TicketTypeRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Exception;

class TicketTypeController extends Controller
{
    public function __construct()
    {
        // Middleware will be handled by routes or attributes
    }

    /**
     * Display a listing of ticket types.
     */
    public function index(Request $request): View
    {
        $workshopId = $request->get('workshop_id');
        $workshop = null;

        $query = TicketType::with(['workshop', 'participants']);

        if ($workshopId) {
            $workshop = Workshop::findOrFail($workshopId);
            $query->where('workshop_id', $workshopId);
        }

        // Add participant counts
        $query->withCount(['participants', 'participants as paid_participants_count' => function ($q) {
            $q->where('is_paid', true);
        }]);

        $ticketTypes = $query->orderBy('workshop_id')->orderBy('name')->paginate(15)->appends(request()->query());

        // Get workshops for filter
        $workshops = Workshop::orderBy('name')->get(['id', 'name']);

        return view('ticket-types.index', compact('ticketTypes', 'workshops', 'workshop'));
    }

    /**
     * Show the form for creating a new ticket type.
     */
    public function create(Request $request): View
    {
        $workshopId = $request->get('workshop_id');
        $workshop = null;

        if ($workshopId) {
            $workshop = Workshop::findOrFail($workshopId);
        }

        $workshops = Workshop::where('status', '!=', 'cancelled')
            ->where('status', '!=', 'completed')
            ->orderBy('name')
            ->get(['id', 'name', 'status']);

        return view('ticket-types.create', compact('workshops', 'workshop'));
    }

    /**
     * Store a newly created ticket type.
     */
    public function store(TicketTypeRequest $request): RedirectResponse
    {
        try {
            $ticketType = TicketType::create($request->validated());

            return redirect()
                ->route('ticket-types.index', ['workshop_id' => $ticketType->workshop_id])
                ->with('success', 'Ticket type created successfully.');

        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create ticket type: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified ticket type.
     */
    public function show(TicketType $ticketType): View
    {
        $ticketType->load(['workshop', 'participants.workshop']);
        
        // Get statistics
        $stats = [
            'total_participants' => $ticketType->participants->count(),
            'paid_participants' => $ticketType->participants->where('is_paid', true)->count(),
            'checked_in_participants' => $ticketType->participants->where('is_checked_in', true)->count(),
            'total_revenue' => $ticketType->participants->where('is_paid', true)->count() * $ticketType->price,
        ];

        return view('ticket-types.show', compact('ticketType', 'stats'));
    }

    /**
     * Show the form for editing the specified ticket type.
     */
    public function edit(TicketType $ticketType): View
    {
        $ticketType->load('workshop');
        
        $workshops = Workshop::where('status', '!=', 'cancelled')
            ->where('status', '!=', 'completed')
            ->orderBy('name')
            ->get(['id', 'name', 'status']);

        return view('ticket-types.edit', compact('ticketType', 'workshops'));
    }

    /**
     * Update the specified ticket type.
     */
    public function update(TicketTypeRequest $request, TicketType $ticketType): RedirectResponse
    {
        try {
            $ticketType->update($request->validated());

            return redirect()
                ->route('ticket-types.show', $ticketType)
                ->with('success', 'Ticket type updated successfully.');

        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update ticket type: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified ticket type.
     */
    public function destroy(TicketType $ticketType): RedirectResponse
    {
        try {
            // Validate deletion constraints
            $errors = app(TicketTypeRequest::class)->validateForDeletion();
            
            if (!empty($errors)) {
                return back()->with('error', implode(' ', $errors));
            }

            $workshopId = $ticketType->workshop_id;
            $ticketType->delete();

            return redirect()
                ->route('ticket-types.index', ['workshop_id' => $workshopId])
                ->with('success', 'Ticket type deleted successfully.');

        } catch (Exception $e) {
            return back()
                ->with('error', 'Failed to delete ticket type: ' . $e->getMessage());
        }
    }

    /**
     * Get ticket types for a workshop (AJAX endpoint).
     */
    public function getByWorkshop(Workshop $workshop)
    {
        $ticketTypes = $workshop->ticketTypes()
            ->withCount(['participants', 'participants as paid_participants_count' => function ($q) {
                $q->where('is_paid', true);
            }])
            ->orderBy('name')
            ->get();

        return response()->json([
            'ticket_types' => $ticketTypes->map(function ($ticketType) {
                return [
                    'id' => $ticketType->id,
                    'name' => Str::limit(strip_tags($ticketType->name), 100),
                    'price' => $ticketType->price,
                    'price_formatted' => '$' . number_format($ticketType->price, 2),
                    'participants_count' => $ticketType->participants_count,
                    'paid_participants_count' => $ticketType->paid_participants_count,
                    'revenue' => $ticketType->paid_participants_count * $ticketType->price,
                ];
            })
        ]);
    }

    /**
     * Get ticket type statistics (AJAX endpoint).
     */
    public function statistics(TicketType $ticketType)
    {
        $ticketType->loadCount([
            'participants',
            'participants as paid_participants_count' => function ($q) {
                $q->where('is_paid', true);
            },
            'participants as checked_in_participants_count' => function ($q) {
                $q->where('is_checked_in', true);
            }
        ]);

        $stats = [
            'id' => $ticketType->id,
            'name' => $ticketType->name,
            'price' => $ticketType->price,
            'price_formatted' => '$' . number_format($ticketType->price, 2),
            'total_participants' => $ticketType->participants_count,
            'paid_participants' => $ticketType->paid_participants_count,
            'unpaid_participants' => $ticketType->participants_count - $ticketType->paid_participants_count,
            'checked_in_participants' => $ticketType->checked_in_participants_count,
            'not_checked_in_participants' => $ticketType->participants_count - $ticketType->checked_in_participants_count,
            'total_revenue' => $ticketType->paid_participants_count * $ticketType->price,
            'potential_revenue' => $ticketType->participants_count * $ticketType->price,
            'payment_rate' => $ticketType->participants_count > 0 
                ? round(($ticketType->paid_participants_count / $ticketType->participants_count) * 100, 1) 
                : 0,
            'check_in_rate' => $ticketType->participants_count > 0 
                ? round(($ticketType->checked_in_participants_count / $ticketType->participants_count) * 100, 1) 
                : 0,
        ];

        return response()->json($stats);
    }

    /**
     * Duplicate a ticket type.
     */
    public function duplicate(TicketType $ticketType): RedirectResponse
    {
        try {
            $duplicatedTicketType = TicketType::create([
                'workshop_id' => $ticketType->workshop_id,
                'name' => Str::limit($ticketType->name, 95, '') . ' (Copy)',
                'price' => $ticketType->price,
            ]);

            return redirect()
                ->route('ticket-types.edit', $duplicatedTicketType)
                ->with('success', 'Ticket type duplicated successfully. Please review and update the details.');

        } catch (Exception $e) {
            return back()
                ->with('error', 'Failed to duplicate ticket type: ' . $e->getMessage());
        }
    }

    /**
     * Check if ticket type can be deleted (AJAX endpoint).
     */
    public function checkDeletion(TicketType $ticketType)
    {
        $participantCount = $ticketType->participants()->count();
        $workshopTicketTypeCount = $ticketType->workshop->ticketTypes()->count();

        $canDelete = $participantCount === 0 && $workshopTicketTypeCount > 1;
        $reasons = [];

        if ($participantCount > 0) {
            $reasons[] = "This ticket type has {$participantCount} participants assigned to it.";
        }

        if ($workshopTicketTypeCount <= 1) {
            $reasons[] = "This is the only ticket type for the workshop.";
        }

        return response()->json([
            'can_delete' => $canDelete,
            'reasons' => $reasons,
            'participant_count' => $participantCount,
            'workshop_ticket_type_count' => $workshopTicketTypeCount,
        ]);
    }
}
