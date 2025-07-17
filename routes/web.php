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

// QR Code Generation - Public route for email templates
Route::get('/qr-code/{ticket_code}', function ($ticket_code) {
    $qrCodeService = app(\App\Services\QrCodeService::class);
    
    // Generate QR code as PNG
    $qrCode = $qrCodeService->generateQrCode($ticket_code, 200);
    
    return response($qrCode)
        ->header('Content-Type', 'image/png')
        ->header('Cache-Control', 'public, max-age=3600'); // Cache for 1 hour
})->name('qr-code.generate');

Route::middleware(['auth', 'verified', 'active'])->group(function () {
    // Dashboard - Available to all authenticated users
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:view dashboard')
        ->name('dashboard');

    // Profile Management - Available to all authenticated users
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Workshop Management
    Route::middleware('permission:view workshops')->group(function () {
        Route::get('/workshops', [WorkshopController::class, 'index'])->name('workshops.index');
        Route::get('/workshops/{workshop}', [WorkshopController::class, 'show'])->name('workshops.show');
        Route::get('/workshops/{workshop}/statistics', [WorkshopController::class, 'statistics'])
            ->middleware('permission:manage workshop statistics')
            ->name('workshops.statistics');
        Route::get('/workshops/{workshop}/participants', [WorkshopController::class, 'participants'])->name('workshops.participants');
    });
    
    Route::middleware('permission:create workshops')->group(function () {
        Route::get('/workshops/create', [WorkshopController::class, 'create'])->name('workshops.create');
        Route::post('/workshops', [WorkshopController::class, 'store'])->name('workshops.store');
        Route::post('/workshops/{workshop}/duplicate', [WorkshopController::class, 'duplicate'])->name('workshops.duplicate');
    });
    
    Route::middleware('permission:edit workshops')->group(function () {
        Route::get('/workshops/{workshop}/edit', [WorkshopController::class, 'edit'])->name('workshops.edit');
        Route::put('/workshops/{workshop}', [WorkshopController::class, 'update'])->name('workshops.update');
    });
    
    Route::delete('/workshops/{workshop}', [WorkshopController::class, 'destroy'])
        ->middleware('permission:delete workshops')
        ->name('workshops.destroy');

    // Participant Management
    Route::middleware('permission:view participants')->group(function () {
        Route::get('/participants', [ParticipantController::class, 'index'])->name('participants.index');
        Route::get('/participants/{participant}', [ParticipantController::class, 'show'])->name('participants.show');
        Route::get('/participants/workshop/{workshop}/ticket-types', [ParticipantController::class, 'getTicketTypes'])->name('participants.ticket-types');
        Route::get('/participants/workshop/{workshop}/stats', [ParticipantController::class, 'getWorkshopStats'])->name('participants.workshop-stats');
    });
    
    Route::middleware('permission:create participants')->group(function () {
        Route::get('/participants/create', [ParticipantController::class, 'create'])->name('participants.create');
        Route::post('/participants', [ParticipantController::class, 'store'])->name('participants.store');
    });
    
    Route::middleware('permission:edit participants')->group(function () {
        Route::get('/participants/{participant}/edit', [ParticipantController::class, 'edit'])->name('participants.edit');
        Route::put('/participants/{participant}', [ParticipantController::class, 'update'])->name('participants.update');
    });
    
    Route::delete('/participants/{participant}', [ParticipantController::class, 'destroy'])
        ->middleware('permission:delete participants')
        ->name('participants.destroy');
    
    Route::middleware('permission:import participants')->group(function () {
        Route::get('/participants/import', [ParticipantController::class, 'import'])->name('participants.import');
        Route::post('/participants/import', [ParticipantController::class, 'processImport'])->name('participants.process-import');
    });
    
    Route::get('/participants/export', [ParticipantController::class, 'export'])
        ->middleware('permission:export participants')
        ->name('participants.export');
    
    Route::middleware('permission:send participant emails')->group(function () {
        Route::post('/participants/{participant}/resend-ticket', [ParticipantController::class, 'resendTicket'])->name('participants.resend-ticket');
        Route::post('/participants/bulk-emails', [ParticipantController::class, 'sendBulkEmails'])->name('participants.bulk-emails');
    });
    
    Route::middleware('permission:manage participant payments')->group(function () {
        Route::post('/participants/{participant}/toggle-payment', [ParticipantController::class, 'togglePayment'])->name('participants.toggle-payment');
        Route::post('/participants/bulk-payment', [ParticipantController::class, 'updatePaymentStatus'])->name('participants.bulk-payment');
    });

    // Ticket Type Management
    Route::middleware('permission:view ticket types')->group(function () {
        Route::get('/ticket-types', [TicketTypeController::class, 'index'])->name('ticket-types.index');
        Route::get('/ticket-types/{ticketType}', [TicketTypeController::class, 'show'])->name('ticket-types.show');
        Route::get('/ticket-types/workshop/{workshop}', [TicketTypeController::class, 'getByWorkshop'])->name('ticket-types.by-workshop');
        Route::get('/ticket-types/{ticketType}/statistics', [TicketTypeController::class, 'statistics'])->name('ticket-types.statistics');
        Route::get('/ticket-types/{ticketType}/check-deletion', [TicketTypeController::class, 'checkDeletion'])->name('ticket-types.check-deletion');
    });
    
    Route::middleware('permission:create ticket types')->group(function () {
        Route::get('/ticket-types/create', [TicketTypeController::class, 'create'])->name('ticket-types.create');
        Route::post('/ticket-types', [TicketTypeController::class, 'store'])->name('ticket-types.store');
        Route::post('/ticket-types/{ticketType}/duplicate', [TicketTypeController::class, 'duplicate'])->name('ticket-types.duplicate');
    });
    
    Route::middleware('permission:edit ticket types')->group(function () {
        Route::get('/ticket-types/{ticketType}/edit', [TicketTypeController::class, 'edit'])->name('ticket-types.edit');
        Route::put('/ticket-types/{ticketType}', [TicketTypeController::class, 'update'])->name('ticket-types.update');
    });
    
    Route::delete('/ticket-types/{ticketType}', [TicketTypeController::class, 'destroy'])
        ->middleware('permission:delete ticket types')
        ->name('ticket-types.destroy');

    // Check-in Management
    Route::middleware('permission:view check-ins')->group(function () {
        Route::get('/check-in', [CheckInController::class, 'index'])->name('check-in.index');
        Route::get('/check-in/workshop/{workshop}/stats', [CheckInController::class, 'getWorkshopStats'])->name('check-in.workshop-stats');
        Route::get('/check-in/workshop/{workshop}/recent', [CheckInController::class, 'getRecentCheckIns'])->name('check-in.recent-checkins');
        Route::get('/check-in/workshop/{workshop}/dashboard', [CheckInController::class, 'dashboard'])->name('check-in.dashboard');
    });
    
    Route::middleware('permission:manage check-ins')->group(function () {
        Route::post('/check-in/participant/{participant}', [CheckInController::class, 'checkInParticipant'])->name('check-in.participant');
        Route::post('/check-in/process', [CheckInController::class, 'processCheckIn'])->name('check-in.process');
    });
    
    Route::middleware('permission:scan qr codes')->group(function () {
        Route::get('/check-in/scanner', [CheckInController::class, 'scanner'])->name('check-in.scanner');
    });
    
    Route::middleware('permission:manual check-in')->group(function () {
        Route::get('/check-in/manual', [CheckInController::class, 'manual'])->name('check-in.manual');
        Route::post('/check-in/manual', [CheckInController::class, 'processManualCheckIn'])->name('check-in.manual.process');
    });
    
    Route::middleware('permission:undo check-ins')->group(function () {
        Route::patch('/check-in/{participant}/undo', [CheckInController::class, 'undoCheckIn'])->name('check-in.undo');
    });
    
    Route::middleware('permission:export check-in reports')->group(function () {
        Route::get('/check-in/workshop/{workshop}/export', [CheckInController::class, 'exportReport'])->name('check-in.export-report');
    });



    // User Management
    Route::middleware('permission:view users')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('/users/roles-permissions', [UserController::class, 'rolesPermissions'])->name('users.roles-permissions');
        Route::get('/users/by-role', [UserController::class, 'getUsersByRole'])->name('users.by-role');
        Route::get('/users/{user}/stats', [UserController::class, 'getUserStats'])->name('users.stats');
    });
    
    Route::middleware('permission:create users')->group(function () {
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
    });
    
    Route::middleware('permission:edit users')->group(function () {
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    });
    
    Route::middleware('permission:manage user roles')->group(function () {
        Route::post('/users/bulk-assign-role', [UserController::class, 'bulkAssignRole'])->name('users.bulk-assign-role');
    });
    
    Route::middleware('permission:activate users,deactivate users')->group(function () {
        Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('/users/bulk-update-status', [UserController::class, 'bulkUpdateStatus'])->name('users.bulk-update-status');
    });
    
    Route::middleware('permission:delete users')->group(function () {
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulk-delete');
    });

    // Email Template Management
    Route::middleware('permission:view email templates')->group(function () {
        Route::get('/workshops/{workshop}/email-templates', [EmailTemplateController::class, 'index'])->name('email-templates.index');
        Route::get('/workshops/{workshop}/email-templates/{emailTemplate}', [EmailTemplateController::class, 'show'])->name('email-templates.show');
        Route::get('/workshops/{workshop}/email-templates/{emailTemplate}/preview', [EmailTemplateController::class, 'preview'])->name('email-templates.preview');
        Route::get('/workshops/{workshop}/email-templates-workshops', [EmailTemplateController::class, 'getWorkshopsForDuplication'])->name('email-templates.workshops');
        Route::get('/email-template-variables', [EmailTemplateController::class, 'variables'])->name('email-templates.variables');
    });
    
    Route::middleware('permission:create email templates')->group(function () {
        Route::get('/workshops/{workshop}/email-templates/create', [EmailTemplateController::class, 'create'])->name('email-templates.create');
        Route::post('/workshops/{workshop}/email-templates', [EmailTemplateController::class, 'store'])->name('email-templates.store');
        Route::post('/workshops/{workshop}/email-templates/{emailTemplate}/duplicate', [EmailTemplateController::class, 'duplicate'])->name('email-templates.duplicate');
    });
    
    Route::middleware('permission:edit email templates')->group(function () {
        Route::get('/workshops/{workshop}/email-templates/{emailTemplate}/edit', [EmailTemplateController::class, 'edit'])->name('email-templates.edit');
        Route::put('/workshops/{workshop}/email-templates/{emailTemplate}', [EmailTemplateController::class, 'update'])->name('email-templates.update');
    });
    
    Route::middleware('permission:delete email templates')->group(function () {
        Route::delete('/workshops/{workshop}/email-templates/{emailTemplate}', [EmailTemplateController::class, 'destroy'])->name('email-templates.destroy');
    });
    
    Route::middleware('permission:send test emails')->group(function () {
        Route::post('/workshops/{workshop}/email-templates/{emailTemplate}/test-send', [EmailTemplateController::class, 'testSend'])->name('email-templates.test-send');
    });
});

require __DIR__.'/auth.php';
