<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use App\Models\User;
use App\Http\Requests\WorkshopRequest;
use App\Services\WorkshopService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;
use Exception;

class WorkshopController extends Controller
{
    protected WorkshopService $workshopService;

    public function __construct(WorkshopService $workshopService)
    {
        $this->workshopService = $workshopService;
    }

    /**
     * Display a listing of workshops.
     */
    public function index(Request $request): View
    {
        $filters = [
            'status' => $request->get('status'),
            'search' => $request->get('search'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
            'organizer_id' => $request->get('organizer_id'),
            'sort_by' => $request->get('sort_by', 'created_at'),
            'sort_order' => $request->get('sort_order', 'desc'),
        ];

        // Filter by user's workshops if not admin
        if (!auth()->user()->can('manage workshops')) {
            $filters['created_by'] = auth()->id();
        }

        $workshops = $this->workshopService->getWorkshops($filters);
        
        // Get organizers for filter dropdown
        $organizers = User::role(['admin', 'organizer'])
            ->active()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('workshops.index', compact('workshops', 'organizers', 'filters'));
    }

    /**
     * Show the form for creating a new workshop.
     */
    public function create(): View
    {
        // Get potential organizers
        $organizers = User::role(['admin', 'organizer'])
            ->active()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('workshops.create', compact('organizers'));
    }

    /**
     * Store a newly created workshop.
     */
    public function store(WorkshopRequest $request): RedirectResponse
    {
        try {
            $workshop = $this->workshopService->createWorkshop($request->validated());

            return redirect()
                ->route('workshops.show', $workshop)
                ->with('success', 'Workshop created successfully.');

        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create workshop: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified workshop.
     */
    public function show(Workshop $workshop): View
    {
        // Check if user can view this workshop
        if (!auth()->user()->can('manage workshops') && 
            $workshop->created_by !== auth()->id() && 
            !$workshop->organizers->contains(auth()->id())) {
            abort(403, 'You do not have permission to view this workshop.');
        }

        $workshop->load(['creator', 'organizers', 'ticketTypes', 'participants.ticketType', 'emailTemplates']);
        
        // Get workshop statistics
        $statistics = $this->workshopService->getWorkshopStatistics($workshop);

        return view('workshops.show', compact('workshop', 'statistics'));
    }

    /**
     * Show the form for editing the specified workshop.
     */
    public function edit(Workshop $workshop): View
    {
        // Check if user can edit this workshop
        if (!auth()->user()->can('manage workshops') && 
            $workshop->created_by !== auth()->id() && 
            !$workshop->organizers->contains(auth()->id())) {
            abort(403, 'You do not have permission to edit this workshop.');
        }

        $workshop->load(['organizers']);
        
        // Get potential organizers
        $organizers = User::role(['admin', 'organizer'])
            ->active()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('workshops.edit', compact('workshop', 'organizers'));
    }

    /**
     * Update the specified workshop.
     */
    public function update(WorkshopRequest $request, Workshop $workshop): RedirectResponse
    {
        // Check if user can edit this workshop
        if (!auth()->user()->can('manage workshops') && 
            $workshop->created_by !== auth()->id() && 
            !$workshop->organizers->contains(auth()->id())) {
            abort(403, 'You do not have permission to edit this workshop.');
        }

        try {
            $updatedWorkshop = $this->workshopService->updateWorkshop($workshop, $request->validated());

            return redirect()
                ->route('workshops.show', $updatedWorkshop)
                ->with('success', 'Workshop updated successfully.');

        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update workshop: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified workshop.
     */
    public function destroy(Workshop $workshop): RedirectResponse
    {
        // Check if user can delete this workshop
        if (!auth()->user()->can('manage workshops') && 
            $workshop->created_by !== auth()->id()) {
            abort(403, 'You do not have permission to delete this workshop.');
        }

        try {
            $this->workshopService->deleteWorkshop($workshop);

            return redirect()
                ->route('workshops.index')
                ->with('success', 'Workshop deleted successfully.');

        } catch (Exception $e) {
            return back()
                ->with('error', 'Failed to delete workshop: ' . $e->getMessage());
        }
    }

    /**
     * Duplicate a workshop.
     */
    public function duplicate(Workshop $workshop): RedirectResponse
    {
        // Check if user can create workshops
        if (!auth()->user()->can('create workshops')) {
            abort(403, 'You do not have permission to create workshops.');
        }

        try {
            $duplicatedWorkshop = $this->workshopService->duplicateWorkshop($workshop, [
                'created_by' => auth()->id(),
            ]);

            return redirect()
                ->route('workshops.edit', $duplicatedWorkshop)
                ->with('success', 'Workshop duplicated successfully. Please review and update the details.');

        } catch (Exception $e) {
            return back()
                ->with('error', 'Failed to duplicate workshop: ' . $e->getMessage());
        }
    }

    /**
     * Update workshop status.
     */
    public function updateStatus(Request $request, Workshop $workshop): RedirectResponse
    {
        // Check if user can edit this workshop
        if (!auth()->user()->can('manage workshops') && 
            $workshop->created_by !== auth()->id() && 
            !$workshop->organizers->contains(auth()->id())) {
            abort(403, 'You do not have permission to edit this workshop.');
        }

        $request->validate([
            'status' => 'required|in:draft,published,ongoing,completed,cancelled'
        ]);

        try {
            $this->workshopService->updateWorkshopStatus($workshop, $request->status);

            return back()
                ->with('success', 'Workshop status updated successfully.');

        } catch (Exception $e) {
            return back()
                ->with('error', 'Failed to update workshop status: ' . $e->getMessage());
        }
    }

    /**
     * Get workshop statistics (AJAX endpoint).
     */
    public function statistics(Workshop $workshop)
    {
        // Check if user can view this workshop
        if (!auth()->user()->can('manage workshops') && 
            $workshop->created_by !== auth()->id() && 
            !$workshop->organizers->contains(auth()->id())) {
            abort(403, 'You do not have permission to view this workshop.');
        }

        $statistics = $this->workshopService->getWorkshopStatistics($workshop);

        return response()->json($statistics);
    }

    /**
     * Get workshop participants (AJAX endpoint).
     */
    public function participants(Workshop $workshop)
    {
        // Check if user can view this workshop
        if (!auth()->user()->can('manage workshops') && 
            $workshop->created_by !== auth()->id() && 
            !$workshop->organizers->contains(auth()->id())) {
            abort(403, 'You do not have permission to view this workshop.');
        }

        $participants = $workshop->participants()
            ->with(['ticketType'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'participants' => $participants->map(function ($participant) {
                return [
                    'id' => $participant->id,
                    'name' => $participant->name,
                    'email' => $participant->email,
                    'ticket_code' => $participant->ticket_code,
                    'ticket_type' => $participant->ticketType->name,
                    'is_paid' => $participant->is_paid,
                    'is_checked_in' => $participant->is_checked_in,
                    'checked_in_at' => $participant->checked_in_at?->format('Y-m-d H:i:s'),
                ];
            })
        ]);
    }

    /**
     * Get workshops for current user (AJAX endpoint).
     */
    public function userWorkshops()
    {
        $workshops = $this->workshopService->getUserWorkshops(auth()->user());

        return response()->json([
            'workshops' => $workshops->map(function ($workshop) {
                return [
                    'id' => $workshop->id,
                    'name' => $workshop->name,
                    'status' => $workshop->status,
                    'start_date' => $workshop->start_date->format('Y-m-d H:i:s'),
                    'participants_count' => $workshop->participants->count(),
                ];
            })
        ]);
    }
}
