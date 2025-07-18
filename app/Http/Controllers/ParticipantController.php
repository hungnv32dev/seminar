<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\Workshop;
use App\Models\TicketType;
use App\Http\Requests\ParticipantRequest;
use App\Services\ParticipantService;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\UploadedFile;
use Exception;

class ParticipantController extends Controller
{
    protected ParticipantService $participantService;
    protected EmailService $emailService;

    public function __construct(ParticipantService $participantService, EmailService $emailService)
    {
        $this->participantService = $participantService;
        $this->emailService = $emailService;
    }

    /**
     * Display a listing of participants.
     */
    public function index(Request $request): View
    {
        $filters = [
            'workshop_id' => $request->get('workshop_id'),
            'ticket_type_id' => $request->get('ticket_type_id'),
            'is_paid' => $request->get('is_paid'),
            'is_checked_in' => $request->get('is_checked_in'),
            'search' => $request->get('search'),
            'sort_by' => $request->get('sort_by', 'created_at'),
            'sort_order' => $request->get('sort_order', 'desc'),
        ];

        $participants = $this->participantService->getParticipants($filters);
        
        // Get workshops and ticket types for filters
        $workshops = Workshop::orderBy('name')->get(['id', 'name']);
        $ticketTypes = TicketType::with('workshop:id,name')->orderBy('name')->get();

        return view('participants.index', compact('participants', 'workshops', 'ticketTypes', 'filters'));
    }

    /**
     * Show the form for creating a new participant.
     */
    public function create(Request $request): View
    {
        $workshopId = $request->get('workshop_id');
        $workshop = null;
        $ticketTypes = collect();

        if ($workshopId) {
            $workshop = Workshop::findOrFail($workshopId);
            $ticketTypes = $workshop->ticketTypes;
        }

        $workshops = Workshop::where('status', '!=', 'cancelled')
            ->where('status', '!=', 'completed')
            ->orderBy('name')
            ->get(['id', 'name', 'status']);

        return view('participants.create', compact('workshops', 'workshop', 'ticketTypes'));
    }

    /**
     * Store a newly created participant.
     */
    public function store(ParticipantRequest $request): RedirectResponse
    {
        try {
            $participant = $this->participantService->createParticipant($request->validated());

            // Send ticket email automatically
            $this->emailService->sendTicketEmail($participant);

            return redirect()
                ->route('participants.show', $participant)
                ->with('success', 'Participant created successfully. Ticket email has been sent.');

        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create participant: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified participant.
     */
    public function show(Participant $participant): View
    {
        $participant->load(['workshop', 'ticketType']);

        return view('participants.show', compact('participant'));
    }

    /**
     * Show the form for editing the specified participant.
     */
    public function edit(Participant $participant): View
    {
        $participant->load(['workshop', 'ticketType']);
        
        $workshops = Workshop::where('status', '!=', 'cancelled')
            ->where('status', '!=', 'completed')
            ->orderBy('name')
            ->get(['id', 'name', 'status']);

        $ticketTypes = $participant->workshop->ticketTypes;

        return view('participants.edit', compact('participant', 'workshops', 'ticketTypes'));
    }

    /**
     * Update the specified participant.
     */
    public function update(ParticipantRequest $request, Participant $participant): RedirectResponse
    {
        try {
            $updatedParticipant = $this->participantService->updateParticipant($participant, $request->validated());

            return redirect()
                ->route('participants.show', $updatedParticipant)
                ->with('success', 'Participant updated successfully.');

        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update participant: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified participant.
     */
    public function destroy(Participant $participant): RedirectResponse
    {
        try {
            $workshopId = $participant->workshop_id;
            $this->participantService->deleteParticipant($participant);

            return redirect()
                ->route('participants.index', ['workshop_id' => $workshopId])
                ->with('success', 'Participant deleted successfully.');

        } catch (Exception $e) {
            return back()
                ->with('error', 'Failed to delete participant: ' . $e->getMessage());
        }
    }

    /**
     * Show import form.
     */
    public function import(Request $request): View
    {
        $workshopId = $request->get('workshop_id');
        $workshop = null;
        $ticketTypes = collect();

        if ($workshopId) {
            $workshop = Workshop::findOrFail($workshopId);
            $ticketTypes = $workshop->ticketTypes;
        }

        $workshops = Workshop::where('status', '!=', 'cancelled')
            ->where('status', '!=', 'completed')
            ->orderBy('name')
            ->get(['id', 'name', 'status']);

        return view('participants.import', compact('workshops', 'workshop', 'ticketTypes'));
    }

    /**
     * Process Excel import.
     */
    public function processImport(Request $request): RedirectResponse
    {
        $request->validate([
            'workshop_id' => 'required|exists:workshops,id',
            'default_ticket_type_id' => 'required|exists:ticket_types,id',
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // 10MB max
        ]);

        try {
            $workshop = Workshop::findOrFail($request->workshop_id);
            $defaultTicketType = TicketType::findOrFail($request->default_ticket_type_id);
            
            // Verify ticket type belongs to workshop
            if ($defaultTicketType->workshop_id !== $workshop->id) {
                throw new Exception('Selected ticket type does not belong to the selected workshop.');
            }

            $result = $this->participantService->importFromExcel(
                $request->file('excel_file'),
                $workshop,
                $defaultTicketType
            );

            // Send ticket emails to imported participants
            if (!empty($result['imported'])) {
                foreach ($result['imported'] as $participant) {
                    try {
                        $this->emailService->sendTicketEmail($participant);
                    } catch (Exception $e) {
                        // Log email error but don't fail the import
                        \Log::error('Failed to send ticket email after import', [
                            'participant_id' => $participant->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }

            $message = "Import completed. {$result['total_imported']} participants imported successfully.";
            
            if ($result['total_errors'] > 0) {
                $message .= " {$result['total_errors']} errors occurred.";
            }
            
            if ($result['total_skipped'] > 0) {
                $message .= " {$result['total_skipped']} participants were skipped.";
            }

            return redirect()
                ->route('participants.index', ['workshop_id' => $workshop->id])
                ->with('success', $message)
                ->with('import_result', $result);

        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to import participants: ' . $e->getMessage());
        }
    }

    /**
     * Resend ticket email.
     */
    public function resendTicket(Participant $participant): RedirectResponse
    {
        try {
            $this->emailService->sendTicketEmail($participant);

            return back()
                ->with('success', 'Ticket email has been resent to ' . $participant->email);

        } catch (Exception $e) {
            return back()
                ->with('error', 'Failed to resend ticket email: ' . $e->getMessage());
        }
    }

    /**
     * Send bulk emails to selected participants.
     */
    public function sendBulkEmails(Request $request): RedirectResponse
    {
        $request->validate([
            'participant_ids' => 'required|array|min:1',
            'participant_ids.*' => 'exists:participants,id',
            'email_type' => 'required|in:ticket,invite,confirm,reminder,thank_you',
        ]);

        try {
            $participants = Participant::whereIn('id', $request->participant_ids)
                ->with(['workshop', 'ticketType'])
                ->get();

            $this->emailService->sendBulkEmails($participants, $request->email_type);

            return back()
                ->with('success', "Bulk {$request->email_type} emails have been queued for {$participants->count()} participants.");

        } catch (Exception $e) {
            return back()
                ->with('error', 'Failed to send bulk emails: ' . $e->getMessage());
        }
    }

    /**
     * Update payment status for multiple participants.
     */
    public function updatePaymentStatus(Request $request): RedirectResponse
    {
        $request->validate([
            'participant_ids' => 'required|array|min:1',
            'participant_ids.*' => 'exists:participants,id',
            'is_paid' => 'required|boolean',
        ]);

        try {
            $updated = $this->participantService->updatePaymentStatus(
                $request->participant_ids,
                $request->is_paid
            );

            $status = $request->is_paid ? 'paid' : 'unpaid';
            
            return back()
                ->with('success', "Updated payment status to {$status} for {$updated} participants.");

        } catch (Exception $e) {
            return back()
                ->with('error', 'Failed to update payment status: ' . $e->getMessage());
        }
    }

    /**
     * Get ticket types for a workshop (AJAX endpoint).
     */
    public function getTicketTypes(Workshop $workshop)
    {
        $ticketTypes = $workshop->ticketTypes()->orderBy('name')->get(['id', 'name', 'price']);

        return response()->json($ticketTypes);
    }

    /**
     * Get participant statistics for a workshop (AJAX endpoint).
     */
    public function getWorkshopStats(Workshop $workshop)
    {
        $stats = $this->participantService->getWorkshopParticipantStats($workshop);

        return response()->json($stats);
    }

    /**
     * Toggle payment status for a participant.
     */
    public function togglePayment(Participant $participant): RedirectResponse
    {
        try {
            $participant->update([
                'is_paid' => !$participant->is_paid
            ]);

            $status = $participant->is_paid ? 'paid' : 'unpaid';

            return back()
                ->with('success', "Payment status updated to {$status} for {$participant->name}.");

        } catch (Exception $e) {
            return back()
                ->with('error', 'Failed to update payment status: ' . $e->getMessage());
        }
    }

    /**
     * Export participants to Excel.
     */
    public function export(Request $request)
    {
        $filters = [
            'workshop_id' => $request->get('workshop_id'),
            'ticket_type_id' => $request->get('ticket_type_id'),
            'is_paid' => $request->get('is_paid'),
            'is_checked_in' => $request->get('is_checked_in'),
            'search' => $request->get('search'),
        ];

        $participants = $this->participantService->getParticipants($filters);

        // For now, return JSON. In a full implementation, you'd use Laravel Excel
        return response()->json([
            'participants' => $participants->map(function ($participant) {
                return [
                    'name' => $participant->name,
                    'email' => $participant->email,
                    'phone' => $participant->phone,
                    'company' => $participant->company,
                    'position' => $participant->position,
                    'workshop' => $participant->workshop->name,
                    'ticket_type' => $participant->ticketType->name,
                    'ticket_code' => $participant->ticket_code,
                    'is_paid' => $participant->is_paid ? 'Yes' : 'No',
                    'is_checked_in' => $participant->is_checked_in ? 'Yes' : 'No',
                    'registration_date' => $participant->created_at->format('Y-m-d H:i:s'),
                ];
            })
        ]);
    }
}
