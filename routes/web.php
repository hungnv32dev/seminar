<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WorkshopController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\TicketTypeController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmailTemplateController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified', 'active'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Workshop Management
    Route::resource('workshops', WorkshopController::class);
    Route::get('/workshops/{workshop}/statistics', [WorkshopController::class, 'statistics'])->name('workshops.statistics');
    Route::post('/workshops/{workshop}/duplicate', [WorkshopController::class, 'duplicate'])->name('workshops.duplicate');
    Route::get('/workshops/{workshop}/participants', [WorkshopController::class, 'participants'])->name('workshops.participants');

    // Participant Management
    Route::resource('participants', ParticipantController::class);
    Route::get('/participants/import', [ParticipantController::class, 'import'])->name('participants.import');
    Route::post('/participants/import', [ParticipantController::class, 'processImport'])->name('participants.process-import');
    Route::post('/participants/{participant}/resend-ticket', [ParticipantController::class, 'resendTicket'])->name('participants.resend-ticket');
    Route::post('/participants/{participant}/toggle-payment', [ParticipantController::class, 'togglePayment'])->name('participants.toggle-payment');
    Route::post('/participants/bulk-emails', [ParticipantController::class, 'sendBulkEmails'])->name('participants.bulk-emails');
    Route::post('/participants/bulk-payment', [ParticipantController::class, 'updatePaymentStatus'])->name('participants.bulk-payment');
    Route::get('/participants/workshop/{workshop}/ticket-types', [ParticipantController::class, 'getTicketTypes'])->name('participants.ticket-types');
    Route::get('/participants/workshop/{workshop}/stats', [ParticipantController::class, 'getWorkshopStats'])->name('participants.workshop-stats');
    Route::get('/participants/export', [ParticipantController::class, 'export'])->name('participants.export');

    // Ticket Type Management
    Route::resource('ticket-types', TicketTypeController::class);
    Route::get('/ticket-types/workshop/{workshop}', [TicketTypeController::class, 'getByWorkshop'])->name('ticket-types.by-workshop');
    Route::get('/ticket-types/{ticketType}/statistics', [TicketTypeController::class, 'statistics'])->name('ticket-types.statistics');
    Route::post('/ticket-types/{ticketType}/duplicate', [TicketTypeController::class, 'duplicate'])->name('ticket-types.duplicate');
    Route::get('/ticket-types/{ticketType}/check-deletion', [TicketTypeController::class, 'checkDeletion'])->name('ticket-types.check-deletion');

    // Check-in Management
    Route::get('/check-in', [CheckInController::class, 'index'])->name('check-in.index');
    Route::get('/check-in/scanner', [CheckInController::class, 'scanner'])->name('check-in.scanner');
    Route::post('/check-in/process', [CheckInController::class, 'processCheckIn'])->name('check-in.process');
    Route::get('/check-in/manual', [CheckInController::class, 'manual'])->name('check-in.manual');
    Route::post('/check-in/manual', [CheckInController::class, 'processManualCheckIn'])->name('check-in.manual.process');
    Route::post('/check-in/participant/{participant}', [CheckInController::class, 'checkInParticipant'])->name('check-in.participant');
    Route::get('/check-in/workshop/{workshop}/stats', [CheckInController::class, 'getWorkshopStats'])->name('check-in.workshop-stats');
    Route::get('/check-in/workshop/{workshop}/recent', [CheckInController::class, 'getRecentCheckIns'])->name('check-in.recent-checkins');
    Route::patch('/check-in/{participant}/undo', [CheckInController::class, 'undoCheckIn'])->name('check-in.undo');
    Route::get('/check-in/workshop/{workshop}/export', [CheckInController::class, 'exportReport'])->name('check-in.export-report');
    Route::get('/check-in/workshop/{workshop}/dashboard', [CheckInController::class, 'dashboard'])->name('check-in.dashboard');

    // User Management
    Route::resource('users', UserController::class);
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::get('/users/roles-permissions', [UserController::class, 'rolesPermissions'])->name('users.roles-permissions');
    Route::get('/users/by-role', [UserController::class, 'getUsersByRole'])->name('users.by-role');
    Route::get('/users/{user}/stats', [UserController::class, 'getUserStats'])->name('users.stats');
    Route::post('/users/bulk-update-status', [UserController::class, 'bulkUpdateStatus'])->name('users.bulk-update-status');
    Route::post('/users/bulk-assign-role', [UserController::class, 'bulkAssignRole'])->name('users.bulk-assign-role');
    Route::post('/users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulk-delete');

    // Email Template Management
    Route::get('/workshops/{workshop}/email-templates', [EmailTemplateController::class, 'index'])->name('email-templates.index');
    Route::get('/workshops/{workshop}/email-templates/create', [EmailTemplateController::class, 'create'])->name('email-templates.create');
    Route::post('/workshops/{workshop}/email-templates', [EmailTemplateController::class, 'store'])->name('email-templates.store');
    Route::get('/workshops/{workshop}/email-templates/{emailTemplate}', [EmailTemplateController::class, 'show'])->name('email-templates.show');
    Route::get('/workshops/{workshop}/email-templates/{emailTemplate}/edit', [EmailTemplateController::class, 'edit'])->name('email-templates.edit');
    Route::put('/workshops/{workshop}/email-templates/{emailTemplate}', [EmailTemplateController::class, 'update'])->name('email-templates.update');
    Route::delete('/workshops/{workshop}/email-templates/{emailTemplate}', [EmailTemplateController::class, 'destroy'])->name('email-templates.destroy');
    Route::get('/workshops/{workshop}/email-templates/{emailTemplate}/preview', [EmailTemplateController::class, 'preview'])->name('email-templates.preview');
    Route::post('/workshops/{workshop}/email-templates/{emailTemplate}/duplicate', [EmailTemplateController::class, 'duplicate'])->name('email-templates.duplicate');
    Route::post('/workshops/{workshop}/email-templates/{emailTemplate}/test-send', [EmailTemplateController::class, 'testSend'])->name('email-templates.test-send');
    Route::get('/workshops/{workshop}/email-templates-workshops', [EmailTemplateController::class, 'getWorkshopsForDuplication'])->name('email-templates.workshops');
    Route::get('/email-template-variables', [EmailTemplateController::class, 'variables'])->name('email-templates.variables');
});

require __DIR__.'/auth.php';
