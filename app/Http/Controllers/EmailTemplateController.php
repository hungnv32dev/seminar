<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use App\Models\Workshop;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Exception;

class EmailTemplateController extends Controller
{
    public function __construct()
    {
        // Apply middleware for permissions
        $this->middleware('can:manage workshops')->only(['index', 'show', 'create', 'store', 'edit', 'update', 'destroy']);
        $this->middleware('can:view workshops')->only(['index', 'show', 'preview']);
        $this->middleware('can:create workshops')->only(['create', 'store']);
        $this->middleware('can:edit workshops')->only(['edit', 'update']);
        $this->middleware('can:delete workshops')->only(['destroy']);
    }

    /**
     * Display a listing of email templates for a workshop.
     */
    public function index(Workshop $workshop): View
    {
        $templates = $workshop->emailTemplates()
            ->orderBy('type')
            ->get();

        $availableTypes = EmailTemplate::TYPES;
        $existingTypes = $templates->pluck('type')->toArray();
        $missingTypes = array_diff(array_keys($availableTypes), $existingTypes);

        return view('email-templates.index', compact('workshop', 'templates', 'availableTypes', 'missingTypes'));
    }

    /**
     * Show the form for creating a new email template.
     */
    public function create(Workshop $workshop, Request $request): View
    {
        $type = $request->get('type');
        
        if (!$type || !array_key_exists($type, EmailTemplate::TYPES)) {
            abort(400, 'Invalid template type');
        }

        // Check if template already exists for this type
        $existingTemplate = $workshop->emailTemplates()->where('type', $type)->first();
        if ($existingTemplate) {
            return redirect()->route('email-templates.edit', [$workshop, $existingTemplate])
                ->with('info', 'Template for this type already exists. You can edit it here.');
        }

        $availableVariables = EmailTemplate::getAvailableVariables();
        $templateType = EmailTemplate::TYPES[$type];

        return view('email-templates.create', compact('workshop', 'type', 'templateType', 'availableVariables'));
    }

    /**
     * Store a newly created email template.
     */
    public function store(Request $request, Workshop $workshop): RedirectResponse
    {
        $request->validate([
            'type' => 'required|string|in:' . implode(',', array_keys(EmailTemplate::TYPES)),
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        try {
            // Check if template already exists for this type
            $existingTemplate = $workshop->emailTemplates()->where('type', $request->type)->first();
            if ($existingTemplate) {
                return back()
                    ->withInput()
                    ->with('error', 'A template for this type already exists for this workshop.');
            }

            $template = $workshop->emailTemplates()->create([
                'type' => $request->type,
                'subject' => $request->subject,
                'content' => $request->content,
            ]);

            return redirect()
                ->route('email-templates.show', [$workshop, $template])
                ->with('success', 'Email template created successfully.');

        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create email template: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified email template.
     */
    public function show(Workshop $workshop, EmailTemplate $emailTemplate): View
    {
        // Ensure the template belongs to the workshop
        if ($emailTemplate->workshop_id !== $workshop->id) {
            abort(404);
        }

        $availableVariables = EmailTemplate::getAvailableVariables();

        return view('email-templates.show', compact('workshop', 'emailTemplate', 'availableVariables'));
    }

    /**
     * Show the form for editing the specified email template.
     */
    public function edit(Workshop $workshop, EmailTemplate $emailTemplate): View
    {
        // Ensure the template belongs to the workshop
        if ($emailTemplate->workshop_id !== $workshop->id) {
            abort(404);
        }

        $availableVariables = EmailTemplate::getAvailableVariables();
        $templateType = EmailTemplate::TYPES[$emailTemplate->type];

        return view('email-templates.edit', compact('workshop', 'emailTemplate', 'availableVariables', 'templateType'));
    }

    /**
     * Update the specified email template.
     */
    public function update(Request $request, Workshop $workshop, EmailTemplate $emailTemplate): RedirectResponse
    {
        // Ensure the template belongs to the workshop
        if ($emailTemplate->workshop_id !== $workshop->id) {
            abort(404);
        }

        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        try {
            $emailTemplate->update([
                'subject' => $request->subject,
                'content' => $request->content,
            ]);

            return redirect()
                ->route('email-templates.show', [$workshop, $emailTemplate])
                ->with('success', 'Email template updated successfully.');

        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update email template: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified email template.
     */
    public function destroy(Workshop $workshop, EmailTemplate $emailTemplate): RedirectResponse
    {
        // Ensure the template belongs to the workshop
        if ($emailTemplate->workshop_id !== $workshop->id) {
            abort(404);
        }

        try {
            $emailTemplate->delete();

            return redirect()
                ->route('email-templates.index', $workshop)
                ->with('success', 'Email template deleted successfully.');

        } catch (Exception $e) {
            return back()
                ->with('error', 'Failed to delete email template: ' . $e->getMessage());
        }
    }

    /**
     * Preview the email template with sample data.
     */
    public function preview(Workshop $workshop, EmailTemplate $emailTemplate): JsonResponse
    {
        // Ensure the template belongs to the workshop
        if ($emailTemplate->workshop_id !== $workshop->id) {
            abort(404);
        }

        // Generate sample data for preview
        $sampleData = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'phone' => '+1234567890',
            'company' => 'Example Corp',
            'position' => 'Software Engineer',
            'ticket_code' => 'WS-' . strtoupper(substr(md5(uniqid()), 0, 8)),
            'qr_code_url' => url('/qr-code/sample'),
            'workshop_name' => $workshop->name,
            'workshop_location' => $workshop->location,
            'workshop_start_date' => $workshop->start_date->format('F j, Y g:i A'),
            'workshop_end_date' => $workshop->end_date->format('F j, Y g:i A'),
            'ticket_type_name' => 'Standard Ticket',
            'ticket_type_price' => '$99.00',
        ];

        $rendered = $emailTemplate->render($sampleData);

        return response()->json([
            'subject' => $rendered['subject'],
            'content' => $rendered['content'],
            'sample_data' => $sampleData,
        ]);
    }

    /**
     * Get template variables documentation.
     */
    public function variables(): JsonResponse
    {
        return response()->json([
            'variables' => EmailTemplate::getAvailableVariables(),
            'usage' => 'Use variables in your template by wrapping them in double curly braces, e.g., {{ name }} or {{ workshop_name }}',
        ]);
    }

    /**
     * Duplicate an email template to another workshop.
     */
    public function duplicate(Request $request, Workshop $workshop, EmailTemplate $emailTemplate): RedirectResponse
    {
        // Ensure the template belongs to the workshop
        if ($emailTemplate->workshop_id !== $workshop->id) {
            abort(404);
        }

        $request->validate([
            'target_workshop_id' => 'required|exists:workshops,id',
        ]);

        try {
            $targetWorkshop = Workshop::findOrFail($request->target_workshop_id);

            // Check if target workshop already has a template of this type
            $existingTemplate = $targetWorkshop->emailTemplates()
                ->where('type', $emailTemplate->type)
                ->first();

            if ($existingTemplate) {
                return back()
                    ->with('error', "Target workshop already has a {$emailTemplate->type_label} template.");
            }

            $duplicatedTemplate = $targetWorkshop->emailTemplates()->create([
                'type' => $emailTemplate->type,
                'subject' => $emailTemplate->subject,
                'content' => $emailTemplate->content,
            ]);

            return back()
                ->with('success', "Template duplicated to {$targetWorkshop->name} successfully.");

        } catch (Exception $e) {
            return back()
                ->with('error', 'Failed to duplicate template: ' . $e->getMessage());
        }
    }

    /**
     * Get all workshops for template duplication.
     */
    public function getWorkshopsForDuplication(Workshop $currentWorkshop): JsonResponse
    {
        $workshops = Workshop::where('id', '!=', $currentWorkshop->id)
            ->where('status', '!=', 'cancelled')
            ->orderBy('name')
            ->get(['id', 'name', 'start_date']);

        return response()->json($workshops);
    }

    /**
     * Test send email template.
     */
    public function testSend(Request $request, Workshop $workshop, EmailTemplate $emailTemplate): RedirectResponse
    {
        // Ensure the template belongs to the workshop
        if ($emailTemplate->workshop_id !== $workshop->id) {
            abort(404);
        }

        $request->validate([
            'test_email' => 'required|email',
        ]);

        try {
            // Generate sample data for test email
            $sampleData = [
                'name' => 'Test User',
                'email' => $request->test_email,
                'phone' => '+1234567890',
                'company' => 'Test Company',
                'position' => 'Test Position',
                'ticket_code' => 'TEST-' . strtoupper(substr(md5(uniqid()), 0, 8)),
                'qr_code_url' => url('/qr-code/test'),
                'workshop_name' => $workshop->name,
                'workshop_location' => $workshop->location,
                'workshop_start_date' => $workshop->start_date->format('F j, Y g:i A'),
                'workshop_end_date' => $workshop->end_date->format('F j, Y g:i A'),
                'ticket_type_name' => 'Test Ticket',
                'ticket_type_price' => '$0.00',
            ];

            $rendered = $emailTemplate->render($sampleData);

            // Send test email using Laravel's Mail facade
            \Mail::raw($rendered['content'], function ($message) use ($rendered, $request) {
                $message->to($request->test_email)
                    ->subject('[TEST] ' . $rendered['subject']);
            });

            return back()
                ->with('success', "Test email sent to {$request->test_email} successfully.");

        } catch (Exception $e) {
            return back()
                ->with('error', 'Failed to send test email: ' . $e->getMessage());
        }
    }
}