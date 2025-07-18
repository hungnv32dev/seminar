<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\Workshop;
use App\Services\ParticipantService;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Exception;

class CheckInController extends Controller
{
    protected ParticipantService $participantService;
    protected QrCodeService $qrCodeService;

    public function __construct(ParticipantService $participantService, QrCodeService $qrCodeService)
    {
        $this->participantService = $participantService;
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Display the check-in interface.
     */
    public function index(Request $request): View
    {
        $workshopId = $request->get('workshop_id');
        $workshop = null;
        $recentCheckIns = collect();

        if ($workshopId) {
            $workshop = Workshop::findOrFail($workshopId);
            
            // Get recent check-ins for this workshop
            $recentCheckIns = $workshop->participants()
                ->where('is_checked_in', true)
                ->with(['ticketType'])
                ->orderBy('checked_in_at', 'desc')
                ->limit(10)
                ->get();
        }

        // Get active workshops for selection
        $workshops = Workshop::whereIn('status', ['published', 'ongoing'])
            ->orderBy('start_date')
            ->get(['id', 'name', 'start_date', 'status']);

        return view('check-in.index', compact('workshops', 'workshop', 'recentCheckIns'));
    }

    /**
     * Show QR code scanner interface.
     */
    public function scanner(Request $request): View
    {
        $workshopId = $request->get('workshop_id');
        $workshop = null;

        if ($workshopId) {
            $workshop = Workshop::findOrFail($workshopId);
        }

        $workshops = Workshop::whereIn('status', ['published', 'ongoing'])
            ->orderBy('start_date')
            ->get(['id', 'name', 'start_date', 'status']);

        return view('check-in.scanner', compact('workshops', 'workshop'));
    }

    /**
     * Process QR code check-in.
     */
    public function processCheckIn(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string',
            'workshop_id' => 'nullable|exists:workshops,id',
        ]);

        try {
            // Decode QR code data
            $qrData = $this->qrCodeService->decodeCheckInQrCode($request->qr_data);
            $ticketCode = $qrData['ticket_code'] ?? $request->qr_data;

            // Validate ticket code
            $validation = $this->qrCodeService->validateTicketCode($ticketCode);

            if (!$validation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => $validation['message'],
                    'participant' => $validation['participant'] ?? null,
                ], 400);
            }

            $participant = $validation['participant'];

            // Check if filtering by workshop
            if ($request->workshop_id && $participant->workshop_id != $request->workshop_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'This ticket is not for the selected workshop.',
                    'participant' => $participant,
                ], 400);
            }

            // Check if already checked in
            if ($validation['already_checked_in']) {
                return response()->json([
                    'success' => true,
                    'message' => $validation['message'],
                    'participant' => $participant,
                    'already_checked_in' => true,
                ]);
            }

            // Perform check-in
            $checkedInParticipant = $this->participantService->checkIn($ticketCode);

            return response()->json([
                'success' => true,
                'message' => 'Check-in successful!',
                'participant' => $checkedInParticipant,
                'already_checked_in' => false,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'participant' => null,
            ], 400);
        }
    }

    /**
     * Manual check-in form.
     */
    public function manual(Request $request): View
    {
        $workshopId = $request->get('workshop_id');
        $workshop = null;

        if ($workshopId) {
            $workshop = Workshop::findOrFail($workshopId);
        }

        $workshops = Workshop::whereIn('status', ['published', 'ongoing'])
            ->orderBy('start_date')
            ->get(['id', 'name', 'start_date', 'status']);

        return view('check-in.manual', compact('workshops', 'workshop'));
    }

    /**
     * Process manual check-in.
     */
    public function processManualCheckIn(Request $request): RedirectResponse
    {
        $request->validate([
            'search' => 'required|string|min:2',
            'workshop_id' => 'nullable|exists:workshops,id',
        ]);

        try {
            $query = Participant::with(['workshop', 'ticketType']);

            // Filter by workshop if specified
            if ($request->workshop_id) {
                $query->where('workshop_id', $request->workshop_id);
            }

            // Search by ticket code, email, or name
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_code', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('name', 'like', '%' . $search . '%');
            });

            $participants = $query->limit(10)->get();

            if ($participants->isEmpty()) {
                return back()
                    ->withInput()
                    ->with('error', 'No participants found matching your search.');
            }

            return back()
                ->withInput()
                ->with('search_results', $participants);

        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Search failed: ' . $e->getMessage());
        }
    }

    /**
     * Check in a specific participant manually.
     */
    public function checkInParticipant(Participant $participant): RedirectResponse
    {
        try {
            if ($participant->is_checked_in) {
                return back()
                    ->with('warning', $participant->name . ' is already checked in at ' . 
                           $participant->checked_in_at->format('Y-m-d H:i:s'));
            }

            $this->participantService->checkIn($participant->ticket_code);

            return back()
                ->with('success', $participant->name . ' has been checked in successfully.');

        } catch (Exception $e) {
            return back()
                ->with('error', 'Failed to check in participant: ' . $e->getMessage());
        }
    }

    /**
     * Get check-in statistics for a workshop (AJAX endpoint).
     */
    public function getWorkshopStats(Workshop $workshop)
    {
        $stats = $this->participantService->getWorkshopParticipantStats($workshop);

        return response()->json([
            'workshop' => [
                'id' => $workshop->id,
                'name' => $workshop->name,
                'status' => $workshop->status,
                'start_date' => $workshop->start_date->format('Y-m-d H:i:s'),
                'location' => $workshop->location,
            ],
            'stats' => $stats,
        ]);
    }

    /**
     * Get recent check-ins for a workshop (AJAX endpoint).
     */
    public function getRecentCheckIns(Workshop $workshop)
    {
        $recentCheckIns = $workshop->participants()
            ->where('is_checked_in', true)
            ->with(['ticketType'])
            ->orderBy('checked_in_at', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'recent_check_ins' => $recentCheckIns->map(function ($participant) {
                return [
                    'id' => $participant->id,
                    'name' => $participant->name,
                    'email' => $participant->email,
                    'ticket_code' => $participant->ticket_code,
                    'ticket_type' => $participant->ticketType->name,
                    'checked_in_at' => $participant->checked_in_at->format('Y-m-d H:i:s'),
                    'checked_in_at_human' => $participant->checked_in_at->diffForHumans(),
                ];
            })
        ]);
    }

    /**
     * Undo check-in for a participant.
     */
    public function undoCheckIn(Participant $participant): RedirectResponse
    {
        try {
            if (!$participant->is_checked_in) {
                return back()
                    ->with('warning', $participant->name . ' is not checked in.');
            }

            $participant->update([
                'is_checked_in' => false,
                'checked_in_at' => null,
            ]);

            return back()
                ->with('success', 'Check-in has been undone for ' . $participant->name);

        } catch (Exception $e) {
            return back()
                ->with('error', 'Failed to undo check-in: ' . $e->getMessage());
        }
    }

    /**
     * Export check-in report for a workshop.
     */
    public function exportReport(Workshop $workshop)
    {
        $participants = $workshop->participants()
            ->with(['ticketType'])
            ->orderBy('checked_in_at', 'desc')
            ->get();

        $stats = $this->participantService->getWorkshopParticipantStats($workshop);

        return response()->json([
            'workshop' => [
                'name' => $workshop->name,
                'start_date' => $workshop->start_date->format('Y-m-d H:i:s'),
                'location' => $workshop->location,
                'status' => $workshop->status,
            ],
            'stats' => $stats,
            'participants' => $participants->map(function ($participant) {
                return [
                    'name' => $participant->name,
                    'email' => $participant->email,
                    'ticket_code' => $participant->ticket_code,
                    'ticket_type' => $participant->ticketType->name,
                    'is_checked_in' => $participant->is_checked_in ? 'Yes' : 'No',
                    'checked_in_at' => $participant->checked_in_at 
                        ? $participant->checked_in_at->format('Y-m-d H:i:s') 
                        : null,
                ];
            })
        ]);
    }

    /**
     * Live check-in dashboard.
     */
    public function dashboard(Workshop $workshop): View
    {
        $workshop->load(['participants.ticketType']);
        
        $stats = $this->participantService->getWorkshopParticipantStats($workshop);
        
        $recentCheckIns = $workshop->participants()
            ->where('is_checked_in', true)
            ->with(['ticketType'])
            ->orderBy('checked_in_at', 'desc')
            ->limit(10)
            ->get();

        return view('check-in.dashboard', compact('workshop', 'stats', 'recentCheckIns'));
    }
}
