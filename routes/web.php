<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\RegistrantAuthController;
use App\Http\Controllers\RegistrantDashboardController;
use App\Http\Controllers\AdminWorkshopController;
use App\Http\Controllers\AdminSpeakerController;
use App\Http\Controllers\AdminTrackController;

use App\Http\Controllers\AdminAgendaController;
use App\Http\Controllers\AdminTimeSlotController;
use App\Http\Controllers\AdminRoomController;
use App\Http\Controllers\AdminFloorController;
use App\Http\Controllers\MailSettingsController;
use App\Http\Controllers\AdminEmailController;
use App\Http\Controllers\EmailLogController;
use App\Http\Controllers\BounceCheckController;
use App\Http\Controllers\AdminConfigController;
use App\Models\AgendaItem;

// Public routes
// Route::get('/', function () {
//     return view('home');
// });
Route::get('/', function () {
    $agendaItems = AgendaItem::ordered()
        ->with('speakers', 'workshop', 'track')
        ->withCount(['registrants as approved_count' => fn ($q) => $q->where('agenda_item_registrant.status', 'approved')])
        ->get();
    $timeSlots = \App\Models\TimeSlot::ordered()->get();
    $rooms = \App\Models\Room::ordered()->get();
    // Group items by time slot key
    $itemMap = [];
    foreach ($agendaItems as $item) {
        $key = $item->start_time . '-' . $item->end_time;
        $itemMap[$key][] = $item;
    }
    $registrationForcedOpen = \Illuminate\Support\Facades\Cache::get('registration_forced_open', false);
    $registrationClosedFull = \Illuminate\Support\Facades\Cache::get('registration_closed_full', false);
    $agendaSectionsVisible = \Illuminate\Support\Facades\Cache::get('agenda_sections_visible', true);
    $workshops = \App\Models\Workshop::withCount(['registrants as approved_count' => function ($q) {
        $q->where('registrant_workshop.status', 'approved');
    }])->orderBy('date')->orderBy('start_time')->get();
    return view('home1', compact('agendaItems', 'timeSlots', 'rooms', 'itemMap', 'registrationForcedOpen', 'registrationClosedFull', 'agendaSectionsVisible', 'workshops'));
})->name('home1');
// ── Test preview page: same layout as home1 but agenda sections are ALWAYS visible ──
// Use this URL to preview the agenda even when the super admin has hidden it publicly.
Route::get('/home1-test', function () {
    $agendaItems = AgendaItem::ordered()
        ->with('speakers', 'workshop', 'track')
        ->withCount(['registrants as approved_count' => fn ($q) => $q->where('agenda_item_registrant.status', 'approved')])
        ->get();
    $timeSlots = \App\Models\TimeSlot::ordered()->get();
    $rooms = \App\Models\Room::ordered()->get();
    // Group items by time slot key
    $itemMap = [];
    foreach ($agendaItems as $item) {
        $key = $item->start_time . '-' . $item->end_time;
        $itemMap[$key][] = $item;
    }
    $registrationForcedOpen = \Illuminate\Support\Facades\Cache::get('registration_forced_open', false);
    $registrationClosedFull = \Illuminate\Support\Facades\Cache::get('registration_closed_full', false);
    // Always show the agenda sections, regardless of the super admin toggle
    $agendaSectionsVisible = true;
    $workshops = \App\Models\Workshop::withCount(['registrants as approved_count' => function ($q) {
        $q->where('registrant_workshop.status', 'approved');
    }])->orderBy('date')->orderBy('start_time')->get();
    return view('home1', compact('agendaItems', 'timeSlots', 'rooms', 'itemMap', 'registrationForcedOpen', 'registrationClosedFull', 'agendaSectionsVisible', 'workshops'));
})->name('home1-test');
// ── Workshop Invitation (public) ──
Route::get('/invitation/workshop/{token}', [App\Http\Controllers\WorkshopInvitationController::class, 'show'])->name('workshop.invitation');
Route::post('/invitation/workshop/{token}', [App\Http\Controllers\WorkshopInvitationController::class, 'register']);

Route::get('/home2', function () {
    return view('home2');
});
Route::get('/home3', function () {
    return view('home3');
});

// ── Registration form submission (public) ──
Route::post('/register', [RegistrantAuthController::class, 'register'])->name('register.submit');
Route::get('/register/success', [RegistrantAuthController::class, 'success'])->name('register.success');

// ── Public walk-in registration (shareable link → status pending) ──
Route::get('/walkin/register', [App\Http\Controllers\WalkinRegisterController::class, 'create'])
    ->name('walkin.public.create')
    ->middleware('throttle:60,1');
Route::post('/walkin/register', [App\Http\Controllers\WalkinRegisterController::class, 'store'])
    ->name('walkin.public.store')
    ->middleware('throttle:10,1');

// ── Feedback (public page; login required to submit) ──
// Short link: /f/{code} redirects to the full feedback URL.
Route::get('/f/{code}', [App\Http\Controllers\FeedbackController::class, 'shortlink'])->name('feedback.short');
Route::get('/feedback/{agendum:slug}', [App\Http\Controllers\FeedbackController::class, 'form'])->name('feedback.form');
Route::middleware('auth:registrant')->group(function () {
    Route::post('/feedback/{agendum:slug}', [App\Http\Controllers\FeedbackController::class, 'store'])->name('feedback.store');
});

// ── QR Scan (public) ──
Route::get('/qr/{token}', [App\Http\Controllers\QrScanController::class, 'scan'])->name('registrant.qr-scan');
Route::post('/qr/{token}/checkin', [App\Http\Controllers\QrScanController::class, 'checkin'])->name('registrant.qr-checkin');
Route::get('/qr-view/{token}', [App\Http\Controllers\QrScanController::class, 'share'])->name('registrant.qr-share');

// ── Client Invite (public setup) ──
Route::get('/client/setup-password/{token}', [App\Http\Controllers\ClientInviteController::class, 'showSetupForm'])->name('client.setup-password');
Route::post('/client/setup-password/{token}', [App\Http\Controllers\ClientInviteController::class, 'savePassword'])->name('client.setup-password');

// ── Admin auth routes (unified — handles Admin & Registrant via role selector) ──
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ── Registrant auth (redirect to unified login) ──
Route::get('/registrant/login', function () {
    return redirect()->route('login');
})->name('registrant.login');
Route::post('/registrant/logout', [RegistrantAuthController::class, 'logout'])->name('registrant.logout');

// ── QR Code Login (public) ──
Route::get('/scan-login', [App\Http\Controllers\QrLoginController::class, 'showForm'])->name('qr-login.form');
Route::post('/scan-login/verify-email', [App\Http\Controllers\QrLoginController::class, 'verifyEmail'])->name('qr-login.verify-email');
Route::post('/scan-login/authenticate', [App\Http\Controllers\QrLoginController::class, 'authenticate'])->name('qr-login.authenticate');

// ── Registrant dashboard (protected) ──
Route::middleware('auth:registrant')->prefix('registrant')->name('registrant.')->group(function () {
    Route::get('/dashboard', [RegistrantDashboardController::class, 'dashboard'])->name('dashboard');
    Route::post('/workshops/{workshop}/register', [RegistrantDashboardController::class, 'registerWorkshop'])->name('workshop.register');
    Route::post('/workshops/{workshop}/unregister', [RegistrantDashboardController::class, 'unregisterWorkshop'])->name('workshop.unregister');
    // Agenda item registration
    Route::post('/agenda/{agendaItem}/register', [RegistrantDashboardController::class, 'registerAgenda'])->name('agenda.register');
    Route::post('/agenda/{agendaItem}/unregister', [RegistrantDashboardController::class, 'unregisterAgenda'])->name('agenda.unregister');
});

// ── Impersonate leave (no auth — controller handles session logic) ──
Route::match(['get', 'post'], '/admin/management/impersonate/leave', [App\Http\Controllers\AdminImpersonateController::class, 'leave'])
    ->name('admin.management.impersonate.leave');

// ── Admin routes (protected by admin middleware) ──
Route::middleware(['auth', 'admin', 'no_cache'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/data', [AdminController::class, 'dashboardData'])->name('dashboard.data');
    Route::get('/dashboard/daily/{date}', [AdminController::class, 'dailyDetail'])->name('dashboard.daily');

    // Regist Confirmation (client recommendations for admin)
    Route::get('/regist-confirmation', [AdminController::class, 'registConfirmation'])->name('regist-confirmation');
    Route::get('/regist-confirmation/unbalanced', [AdminController::class, 'registConfirmationUnbalanced'])->name('regist-confirmation.unbalanced');
    Route::get('/regist-confirmation/export/csv', [AdminController::class, 'exportRegistConfirmationCsv'])->name('regist-confirmation.export-csv');

    // Import client confirmations from Excel (super admin only)
    Route::get('/import-client-confirmations', [App\Http\Controllers\AdminImportController::class, 'index'])->name('import-client-confirmations');
    Route::post('/import-client-confirmations/preview', [App\Http\Controllers\AdminImportController::class, 'preview'])->name('import-client-confirmations.preview');
    Route::post('/import-client-confirmations/apply', [App\Http\Controllers\AdminImportController::class, 'apply'])->name('import-client-confirmations.apply');
    Route::post('/import-client-confirmations/cancel', [App\Http\Controllers\AdminImportController::class, 'cancel'])->name('import-client-confirmations.cancel');

    // Registrant management
    Route::get('/registrants', [AdminController::class, 'index'])->name('registrants.index');
    Route::get('/registrants/search', [AdminController::class, 'search'])->name('registrants.search');
    Route::get('/registrants/realtime', [AdminController::class, 'registrantsRealtime'])->name('registrants.realtime');
    Route::get('/registrants/rows', [AdminController::class, 'registrantsRows'])->name('registrants.rows');
    Route::post('/registrants/pending-sync', [AdminController::class, 'pendingSync'])->name('registrants.pending-sync');
    Route::get('/registrants/{registrant}', [AdminController::class, 'show'])->name('registrants.show');
    Route::get('/registrants/{registrant}/edit', [AdminController::class, 'edit'])->name('registrants.edit');
    Route::put('/registrants/{registrant}', [AdminController::class, 'update'])->name('registrants.update');
    Route::delete('/registrants/{registrant}', [AdminController::class, 'destroy'])->name('registrants.destroy');
    Route::post('/registrants/{registrant}/approve', [AdminController::class, 'approve'])->name('registrants.approve');
    Route::post('/registrants/{registrant}/approve-reminder', [AdminController::class, 'approveWithReminder'])->name('registrants.approve-reminder');
    Route::post('/registrants/{registrant}/reject', [AdminController::class, 'reject'])->name('registrants.reject');
    Route::post('/registrants/{registrant}/toggle-waitlist', [AdminController::class, 'toggleWaitlist'])->name('registrants.toggle-waitlist');
    Route::post('/registrants/{registrant}/change-remark', [AdminController::class, 'changeWaitlistMark'])->name('registrants.change-remark');
    Route::post('/registrants/{registrant}/resend-credentials', [AdminController::class, 'resendCredentials'])->name('registrants.resend-credentials');
    Route::post('/registrants/bulk-approve', [AdminController::class, 'bulkApprove'])->name('registrants.bulk-approve');
    Route::post('/registrants/bulk-approve-reminder', [AdminController::class, 'bulkApproveWithReminder'])->name('registrants.bulk-approve-reminder');
    Route::post('/registrants/bulk-reject', [AdminController::class, 'bulkReject'])->name('registrants.bulk-reject');
    Route::post('/registrants/submit-decisions', [AdminController::class, 'submitClientDecisions'])->name('registrants.submit-decisions');
    Route::get('/registrants/export/csv', [AdminController::class, 'exportCsv'])->name('registrants.export-csv');
    Route::get('/registrants/export/crawling', [AdminController::class, 'exportCrawlingTxt'])->name('registrants.export-crawling');
    Route::post('/registrants/{registrant}/notes', [AdminController::class, 'updateNotes'])->name('registrants.notes');
    Route::post('/registrants/{registrant}/client-remark', [AdminController::class, 'clientRemark'])->name('registrants.client-remark');
    Route::post('/registrants/{registrant}/send-email/{type}', [AdminController::class, 'sendEmailByType'])->name('registrants.send-email');

    // ── Feedback (Registrants) — read-only viewer/admin page ──
    Route::get('/feedback-registrants', [App\Http\Controllers\AdminFeedbackRegistrantsController::class, 'index'])->name('feedback-registrants.index');
    Route::get('/feedback-registrants/search', [App\Http\Controllers\AdminFeedbackRegistrantsController::class, 'search'])->name('feedback-registrants.search');
    Route::post('/feedback-registrants/qr-lookup', [App\Http\Controllers\AdminFeedbackRegistrantsController::class, 'qrLookup'])->name('feedback-registrants.qr-lookup');
    Route::get('/feedback-registrants/{registrant}', [App\Http\Controllers\AdminFeedbackRegistrantsController::class, 'show'])->name('feedback-registrants.show');

    // ── Walk-in Registration ──
    Route::get('/walkin', [AdminController::class, 'walkinForm'])->name('walkin.form');
    Route::post('/walkin', [AdminController::class, 'walkinStore'])->name('walkin.store');
    Route::get('/walkin/{registrant}', [AdminController::class, 'walkinShow'])->name('walkin.show');

    // ── Onsite Event (participant list + name badge printing) — admin & super admin ──
    Route::get('/onsite', [App\Http\Controllers\AdminOnsiteController::class, 'index'])->name('onsite');
    Route::get('/onsite/search', [App\Http\Controllers\AdminOnsiteController::class, 'search'])->name('onsite.search');
    Route::get('/onsite/mqtt-status', [App\Http\Controllers\AdminOnsiteController::class, 'mqttStatus'])->name('onsite.mqtt-status');
    Route::get('/onsite/badges/print', [App\Http\Controllers\AdminOnsiteController::class, 'printBadges'])->name('onsite.badges');
    Route::post('/onsite/badges/trigger', [App\Http\Controllers\AdminOnsiteController::class, 'triggerPrint'])->name('onsite.badges.trigger');

    // ── Super Admin only sections ──
    Route::middleware('super_admin')->group(function () {

    // Registration Form Toggle
    Route::post('/toggle-registration', [AdminController::class, 'toggleRegistration'])->name('toggle-registration');

    // Registration Form — close because event reached full capacity
    Route::post('/toggle-registration-full', [AdminController::class, 'toggleRegistrationFull'])->name('toggle-registration-full');

    // New Agenda Section (Schedule tabs) visibility toggle
    Route::post('/toggle-agenda-sections', [AdminController::class, 'toggleAgendaSections'])->name('toggle-agenda-sections');

    // Event Checker app config + QR code
    Route::get('/app-config', [AdminConfigController::class, 'show'])->name('app-config');

    // Mobile app room accounts + per-session assignment (super admin only)
    Route::get('/room-accounts', [App\Http\Controllers\AdminRoomAccountController::class, 'index'])->name('room-accounts.index');
    Route::post('/room-accounts', [App\Http\Controllers\AdminRoomAccountController::class, 'store'])->name('room-accounts.store');
    Route::get('/room-accounts/{user}/sessions', [App\Http\Controllers\AdminRoomAccountController::class, 'sessions'])->name('room-accounts.sessions');
    Route::post('/room-accounts/{user}/sessions', [App\Http\Controllers\AdminRoomAccountController::class, 'saveSessions'])->name('room-accounts.sessions.store');

    // Mail Settings
    Route::get('/mail-settings', [MailSettingsController::class, 'edit'])->name('mail-settings.edit');
    Route::post('/mail-settings', [MailSettingsController::class, 'update'])->name('mail-settings.update');
    Route::post('/mail-settings/test', [MailSettingsController::class, 'test'])->name('mail-settings.test');

    // Email Templates
    Route::get('/templates', [EmailTemplateController::class, 'index'])->name('templates.index');
    Route::get('/templates/upload', [EmailTemplateController::class, 'uploadForm'])->name('templates.upload');
    Route::post('/templates/upload', [EmailTemplateController::class, 'upload'])->name('templates.upload.store');
    Route::get('/templates/create', [EmailTemplateController::class, 'create'])->name('templates.create');
    Route::get('/templates/create-from-fallback', [EmailTemplateController::class, 'createFromFallback'])->name('templates.create-from-fallback');
    Route::post('/templates', [EmailTemplateController::class, 'store'])->name('templates.store');
    Route::get('/templates/{template}/edit', [EmailTemplateController::class, 'edit'])->name('templates.edit');
    Route::put('/templates/{template}', [EmailTemplateController::class, 'update'])->name('templates.update');
    Route::delete('/templates/{template}', [EmailTemplateController::class, 'destroy'])->name('templates.destroy');
    Route::post('/templates/{template}/toggle', [EmailTemplateController::class, 'toggleActive'])->name('templates.toggle');
    Route::get('/templates/{template}/preview', [EmailTemplateController::class, 'preview'])->name('templates.preview');
    Route::get('/templates/{template}/logs', [EmailTemplateController::class, 'logs'])->name('templates.logs');
    Route::match(['get', 'post'], '/templates/reminder/send', [EmailTemplateController::class, 'sendReminder'])->name('templates.send-reminder');
    Route::post('/templates/toggle-auto-email', [EmailTemplateController::class, 'toggleAutoEmail'])->name('templates.toggle-auto-email');

    // Admin Emails (test email recipients)
    Route::get('/admin-emails', [AdminEmailController::class, 'index'])->name('admin-emails.index');
    Route::get('/admin-emails/create', [AdminEmailController::class, 'create'])->name('admin-emails.create');
    Route::post('/admin-emails', [AdminEmailController::class, 'store'])->name('admin-emails.store');
    Route::get('/admin-emails/{adminEmail}/edit', [AdminEmailController::class, 'edit'])->name('admin-emails.edit');
    Route::put('/admin-emails/{adminEmail}', [AdminEmailController::class, 'update'])->name('admin-emails.update');
    Route::delete('/admin-emails/{adminEmail}', [AdminEmailController::class, 'destroy'])->name('admin-emails.destroy');
    Route::post('/admin-emails/send-test', [AdminEmailController::class, 'sendTest'])->name('admin-emails.send-test');

    // Bounce Check
    Route::post('/bounce-check', [BounceCheckController::class, 'run'])->name('bounce-check.run');

    // Workshop CRUD (super_admin only)
    Route::get('/workshops/create', [AdminWorkshopController::class, 'create'])->name('workshops.create');
    Route::post('/workshops', [AdminWorkshopController::class, 'store'])->name('workshops.store');
    Route::get('/workshops/{workshop}/edit', [AdminWorkshopController::class, 'edit'])->name('workshops.edit');
    Route::put('/workshops/{workshop}', [AdminWorkshopController::class, 'update'])->name('workshops.update');
    Route::delete('/workshops/{workshop}', [AdminWorkshopController::class, 'destroy'])->name('workshops.destroy');
    Route::post('/workshops/{workshop}/toggle', [AdminWorkshopController::class, 'toggleRegistration'])->name('workshops.toggle');
    Route::post('/workshops/{workshop}/toggle-bypass', [AdminWorkshopController::class, 'toggleInvitationBypass'])->name('workshops.toggle-bypass');
    // Workshop Invitations
    Route::get('/workshops/{workshop}/invitations', [App\Http\Controllers\WorkshopInvitationController::class, 'index'])->name('workshops.invitations');
    Route::post('/workshops/{workshop}/invitations/generate', [App\Http\Controllers\WorkshopInvitationController::class, 'generate'])->name('workshops.invitations.generate');
    Route::post('/invitations/{invitation}/toggle', [App\Http\Controllers\WorkshopInvitationController::class, 'toggle'])->name('workshops.invitations.toggle');
    Route::post('/invitations/{invitation}/update-max-uses', [App\Http\Controllers\WorkshopInvitationController::class, 'updateMaxUses'])->name('workshops.invitations.update-max-uses');
    // Workshop Tracks
    Route::get('/workshops/{workshop}/tracks', [App\Http\Controllers\AdminWorkshopTrackController::class, 'index'])->name('workshops.tracks');
    Route::post('/workshops/{workshop}/tracks', [App\Http\Controllers\AdminWorkshopTrackController::class, 'store'])->name('workshops.tracks.store');
    Route::put('/workshops/{workshop}/tracks/{track}', [App\Http\Controllers\AdminWorkshopTrackController::class, 'update'])->name('workshops.tracks.update');
    Route::post('/workshops/{workshop}/tracks/{track}/toggle', [App\Http\Controllers\AdminWorkshopTrackController::class, 'toggle'])->name('workshops.tracks.toggle');
    Route::delete('/workshops/{workshop}/tracks/{track}', [App\Http\Controllers\AdminWorkshopTrackController::class, 'destroy'])->name('workshops.tracks.destroy');
    // General Sessions management (super admin only)
    Route::get('/general-sessions', [App\Http\Controllers\AdminGeneralSessionController::class, 'index'])->name('general-sessions.index');
    Route::get('/general-sessions/create', [App\Http\Controllers\AdminGeneralSessionController::class, 'create'])->name('general-sessions.create');
    Route::post('/general-sessions', [App\Http\Controllers\AdminGeneralSessionController::class, 'store'])->name('general-sessions.store');
    Route::get('/general-sessions/{agendum}/edit', [App\Http\Controllers\AdminGeneralSessionController::class, 'edit'])->name('general-sessions.edit');
    Route::put('/general-sessions/{agendum}', [App\Http\Controllers\AdminGeneralSessionController::class, 'update'])->name('general-sessions.update');
    Route::delete('/general-sessions/{agendum}', [App\Http\Controllers\AdminGeneralSessionController::class, 'destroy'])->name('general-sessions.destroy');
    // Agenda management
    Route::get('/agenda', [AdminAgendaController::class, 'index'])->name('agenda.index');
    Route::get('/agenda/create', [AdminAgendaController::class, 'create'])->name('agenda.create');
    Route::post('/agenda', [AdminAgendaController::class, 'store'])->name('agenda.store');
    Route::get('/agenda/{agendum}/edit', [AdminAgendaController::class, 'edit'])->name('agenda.edit');
    Route::put('/agenda/{agendum}', [AdminAgendaController::class, 'update'])->name('agenda.update');
    Route::delete('/agenda/{agendum}', [AdminAgendaController::class, 'destroy'])->name('agenda.destroy');
    Route::post('/agenda/{agendum}/merge', [AdminAgendaController::class, 'merge'])->name('agenda.merge');
    // Feedback management
    Route::post('/agenda/{agendum}/feedback/toggle', [App\Http\Controllers\AdminFeedbackController::class, 'toggle'])->name('agenda.feedback.toggle');
    Route::get('/feedback', [App\Http\Controllers\AdminFeedbackController::class, 'index'])->name('feedback.index');
    Route::get('/feedback/export/excel', [App\Http\Controllers\AdminFeedbackController::class, 'exportExcel'])->name('feedback.export-excel');
    Route::get('/agenda/{agendum}/feedback', [App\Http\Controllers\AdminFeedbackController::class, 'show'])->name('agenda.feedback.show');
    Route::get('/agenda/{agendum}/feedback/export/csv', [App\Http\Controllers\AdminFeedbackController::class, 'exportCsv'])->name('agenda.feedback.export-csv');
    // Feedback Templates
    Route::get('/feedback-templates', [App\Http\Controllers\FeedbackTemplateController::class, 'index'])->name('feedback.templates');
    Route::get('/feedback-templates/create', [App\Http\Controllers\FeedbackTemplateController::class, 'create'])->name('feedback.templates.create');
    Route::post('/feedback-templates', [App\Http\Controllers\FeedbackTemplateController::class, 'store'])->name('feedback.templates.store');
    Route::get('/feedback-templates/{template}/edit', [App\Http\Controllers\FeedbackTemplateController::class, 'edit'])->name('feedback.templates.edit');
    Route::put('/feedback-templates/{template}', [App\Http\Controllers\FeedbackTemplateController::class, 'update'])->name('feedback.templates.update');
    Route::delete('/feedback-templates/{template}', [App\Http\Controllers\FeedbackTemplateController::class, 'destroy'])->name('feedback.templates.destroy');
    // Apply template to agenda
    Route::get('/agenda/{agendum}/feedback/questions', [App\Http\Controllers\FeedbackTemplateController::class, 'applyForm'])->name('agenda.feedback.questions');
    Route::post('/agenda/{agendum}/feedback/apply-template', [App\Http\Controllers\FeedbackTemplateController::class, 'applyStore'])->name('agenda.feedback.apply-template');
    Route::post('/agenda/{agendum}/feedback/questions/clear', [App\Http\Controllers\FeedbackTemplateController::class, 'clearQuestions'])->name('agenda.feedback.questions.clear');
    Route::put('/agenda/{agendum}/feedback/questions/{question}', [App\Http\Controllers\FeedbackTemplateController::class, 'updateQuestion'])->name('agenda.feedback.questions.update');
    // Time Slots management
    Route::get('/time-slots', [AdminTimeSlotController::class, 'index'])->name('time-slots.index');
    Route::post('/time-slots', [AdminTimeSlotController::class, 'store'])->name('time-slots.store');
    Route::put('/time-slots/{timeSlot}', [AdminTimeSlotController::class, 'update'])->name('time-slots.update');
    Route::delete('/time-slots/{timeSlot}', [AdminTimeSlotController::class, 'destroy'])->name('time-slots.destroy');

    // Rooms & Floors consolidated management
    Route::get('/rooms', [AdminRoomController::class, 'index'])->name('rooms.index');
    Route::post('/rooms', [AdminRoomController::class, 'store'])->name('rooms.store');
    Route::put('/rooms/{room}', [AdminRoomController::class, 'update'])->name('rooms.update');
    Route::delete('/rooms/{room}', [AdminRoomController::class, 'destroy'])->name('rooms.destroy');
    Route::post('/rooms/floor', [AdminRoomController::class, 'storeFloor'])->name('rooms.floor.store');
    Route::put('/rooms/floor/{floor}', [AdminRoomController::class, 'updateFloor'])->name('rooms.floor.update');
    Route::delete('/rooms/floor/{floor}', [AdminRoomController::class, 'destroyFloor'])->name('rooms.floor.destroy');

    // (Legacy floor routes redirect)
    Route::get('/floors', fn() => redirect()->route('admin.rooms.index'))->name('floors.index');
    Route::post('/floors', fn() => redirect()->route('admin.rooms.index'));
    Route::put('/floors/{floor}', fn() => redirect()->route('admin.rooms.index'));
    Route::delete('/floors/{floor}', fn() => redirect()->route('admin.rooms.index'));

    // Speakers management
    Route::get('/speakers', [AdminSpeakerController::class, 'index'])->name('speakers.index');
    Route::post('/speakers', [AdminSpeakerController::class, 'store'])->name('speakers.store');
    Route::put('/speakers/{speaker}', [AdminSpeakerController::class, 'update'])->name('speakers.update');
    Route::delete('/speakers/{speaker}', [AdminSpeakerController::class, 'destroy'])->name('speakers.destroy');
    Route::post('/speakers/{speaker}/toggle', [AdminSpeakerController::class, 'toggle'])->name('speakers.toggle');

    // Tracks CRUD (super_admin only)
    Route::post('/tracks', [AdminTrackController::class, 'store'])->name('tracks.store');
    Route::put('/tracks/{track}', [AdminTrackController::class, 'update'])->name('tracks.update');
    Route::delete('/tracks/{track}', [AdminTrackController::class, 'destroy'])->name('tracks.destroy');
    Route::post('/tracks/{track}/toggle', [AdminTrackController::class, 'toggle'])->name('tracks.toggle');
    });

    // ── Workshop UTM Links (separate from event UTM) — accessible by admin & client ──
    Route::get('/workshops/utm-links', [App\Http\Controllers\AdminManagementController::class, 'workshopUtmLinks'])->name('workshops.utm-links');
    Route::get('/workshops/utm-links/export', [App\Http\Controllers\AdminManagementController::class, 'exportWorkshopUtmLinks'])->name('workshops.utm-links.export');
    Route::get('/workshops/utm-links/{utmLink}/registrants', [App\Http\Controllers\AdminManagementController::class, 'utmLinkRegistrants'])->name('workshops.utm-links.registrants');
    Route::get('/workshops/utm-links/{utmLink}/registrants/export/csv', [App\Http\Controllers\AdminManagementController::class, 'utmLinkRegistrantsCsv'])->name('workshops.utm-links.registrants-export');
    Route::post('/workshops/utm-links', [App\Http\Controllers\AdminManagementController::class, 'storeWorkshopUtmLink'])->name('workshops.utm-links.store');
    Route::put('/workshops/utm-links/{utmLink}', [App\Http\Controllers\AdminManagementController::class, 'updateWorkshopUtmLink'])->name('workshops.utm-links.update');
    Route::delete('/workshops/utm-links/{utmLink}', [App\Http\Controllers\AdminManagementController::class, 'destroyWorkshopUtmLink'])->name('workshops.utm-links.destroy');

    // ── Agenda QR Scan — accessible by users with agenda permission ──
    Route::get('/agenda/scan', [AdminAgendaController::class, 'scanIndex'])->name('agenda.scan-index');
    Route::get('/agenda/{agendum}/scan', [AdminAgendaController::class, 'scan'])->name('agenda.scan');
    Route::post('/agenda/{agendum}/scan', [AdminAgendaController::class, 'scanProcess'])->name('agenda.scan-process');
    Route::get('/agenda/{agendum}/visitors', [AdminAgendaController::class, 'visitors'])->name('agenda.visitors');
    Route::get('/agenda/{agendum}/visitors/export/csv', [AdminAgendaController::class, 'exportVisitorsCsv'])->name('agenda.visitors.export-csv');

    // ── Email Logs — accessible by users with email_templates permission ──
    Route::get('/email-logs', [EmailLogController::class, 'index'])->name('email-logs.index');
    Route::get('/email-logs/export/csv', [EmailLogController::class, 'exportCsv'])->name('email-logs.export-csv');
    Route::get('/email-logs/{emailLog}', [EmailLogController::class, 'show'])->name('email-logs.show');
    Route::post('/email-logs/{emailLog}/resend', [EmailLogController::class, 'resend'])->name('email-logs.resend');

    // ── Send Reminder — accessible by users with email_templates permission ──
    Route::get('/send-reminder', [EmailLogController::class, 'reminderForm'])->name('email-logs.reminder-form');
    Route::post('/send-reminder', [EmailLogController::class, 'sendReminder'])->name('email-logs.send-reminder');

    // ── Track registrants approve/reject — accessible by admin + super_admin ──
    Route::post('/tracks/{track}/registrants/{registrant}/approve', [AdminTrackController::class, 'approveRegistrant'])->name('tracks.registrants.approve');
    Route::post('/tracks/{track}/registrants/{registrant}/reject', [AdminTrackController::class, 'rejectRegistrant'])->name('tracks.registrants.reject');

    // ── Workshop & Track Viewing — accessible by all admin roles (including client) ──
    Route::get('/workshops', [AdminWorkshopController::class, 'index'])->name('workshops.index');
    Route::get('/workshops/{workshop}/registrants', [AdminWorkshopController::class, 'registrants'])->name('workshops.registrants');
    Route::get('/workshops/{workshop}/registrants/export/csv', [AdminWorkshopController::class, 'exportWorkshopCsv'])->name('workshops.registrants.export-csv');
    Route::get('/tracks', [AdminTrackController::class, 'index'])->name('tracks.index');
    Route::get('/tracks/monitoring', [AdminTrackController::class, 'monitoring'])->name('tracks.monitoring');
    Route::get('/tracks/monitoring/export', [AdminTrackController::class, 'monitoringExport'])->name('tracks.monitoring.export');
    Route::get('/tracks/{track}/registrants', [AdminTrackController::class, 'registrants'])->name('tracks.registrants');
    Route::get('/tracks/{track}/visitors', [AdminTrackController::class, 'visitors'])->name('tracks.visitors');
    Route::get('/tracks/{track}/export', [AdminTrackController::class, 'exportTrackCsv'])->name('tracks.export');

    // ── Workshop Registrants Management (admin + super_admin) ──
    Route::get('/workshop-registrants', [AdminWorkshopController::class, 'workshopRegistrants'])->name('workshop-registrants.index');
    Route::get('/workshop-registrants/export/csv', [AdminWorkshopController::class, 'exportCsv'])->name('workshop-registrants.export-csv');
    Route::post('/workshops/{workshop}/registrants/{registrant}/approve', [AdminWorkshopController::class, 'approveRegistrant'])->name('workshops.registrants.approve');
    Route::post('/workshops/{workshop}/registrants/{registrant}/reject', [AdminWorkshopController::class, 'rejectRegistrant'])->name('workshops.registrants.reject');
    Route::post('/workshops/{workshop}/registrants/{registrant}/track', [AdminWorkshopController::class, 'assignTrack'])->name('workshops.registrants.track');
    Route::post('/workshops/{workshop}/send-reminder', [AdminWorkshopController::class, 'sendReminder'])->name('workshops.send-reminder');

    // ── Agenda Registrants — accessible by all admin roles ──
    Route::get('/agenda-registrants', [AdminAgendaController::class, 'registrantsIndex'])->name('agenda-registrants.index');
    Route::get('/agenda-registrants/{agendum}', [AdminAgendaController::class, 'registrantsDetail'])->name('agenda-registrants.detail');
    Route::post('/agenda-registrants/{agendum}/registrants/{registrant}/approve', [AdminAgendaController::class, 'registrantsApprove'])->name('agenda-registrants.approve');
    Route::post('/agenda-registrants/{agendum}/registrants/{registrant}/reject', [AdminAgendaController::class, 'registrantsReject'])->name('agenda-registrants.reject');

    // ── Booths & QR Scan — accessible by users with booths permission ──
    Route::get('/booths', [App\Http\Controllers\AdminBoothController::class, 'index'])->name('booths.index');
    Route::get('/booths/create', [App\Http\Controllers\AdminBoothController::class, 'create'])->name('booths.create');
    Route::post('/booths', [App\Http\Controllers\AdminBoothController::class, 'store'])->name('booths.store');
    Route::get('/booths/{booth}/edit', [App\Http\Controllers\AdminBoothController::class, 'edit'])->name('booths.edit');
    Route::put('/booths/{booth}', [App\Http\Controllers\AdminBoothController::class, 'update'])->name('booths.update');
    Route::delete('/booths/{booth}', [App\Http\Controllers\AdminBoothController::class, 'destroy'])->name('booths.destroy');
    Route::post('/booths/{booth}/toggle', [App\Http\Controllers\AdminBoothController::class, 'toggle'])->name('booths.toggle');
    Route::get('/booths/{booth}/scan', [App\Http\Controllers\AdminBoothController::class, 'scan'])->name('booths.scan');
    Route::post('/booths/{booth}/scan', [App\Http\Controllers\AdminBoothController::class, 'scanProcess'])->name('booths.scan-process');
    Route::get('/booths/{booth}/visitors', [App\Http\Controllers\AdminBoothController::class, 'visitors'])->name('booths.visitors');
    Route::get('/booths/{booth}/visitors/export/csv', [App\Http\Controllers\AdminBoothController::class, 'exportVisitorsCsv'])->name('booths.visitors.export-csv');

    // ── Management (UTM - all admins, scoped) ──
    Route::prefix('management')->name('management.')->group(function () {
        Route::get('/utm-sources', [App\Http\Controllers\AdminManagementController::class, 'utmSources'])->name('utm');
        Route::get('/utm-sources/export/csv', [App\Http\Controllers\AdminManagementController::class, 'exportUtmCsv'])->name('utm.export-csv');
        Route::post('/utm-links', [App\Http\Controllers\AdminManagementController::class, 'storeUtmLink'])->name('utm-links.store');
        Route::put('/utm-links/{utmLink}', [App\Http\Controllers\AdminManagementController::class, 'updateUtmLink'])->name('utm-links.update');
        Route::delete('/utm-links/{utmLink}', [App\Http\Controllers\AdminManagementController::class, 'destroyUtmLink'])->name('utm-links.destroy');

        // QR Codes — all admins can view
        Route::get('/qr-codes', [App\Http\Controllers\AdminManagementController::class, 'qrCodes'])->name('qr');
        Route::get('/qr-codes/export/csv', [App\Http\Controllers\AdminManagementController::class, 'exportQrCsv'])->name('qr.export-csv');

            // Check-in, Users, Login Logs — super_admin only
            Route::middleware('super_admin')->group(function () {
                Route::get('/checkin-log', [App\Http\Controllers\AdminManagementController::class, 'checkinLog'])->name('checkin');
                Route::get('/checkin-log/export/csv', [App\Http\Controllers\AdminManagementController::class, 'exportCheckinCsv'])->name('checkin.export-csv');
                // Login Logs
                Route::get('/login-logs', [App\Http\Controllers\AdminLoginLogController::class, 'index'])->name('login-logs');
                Route::get('/login-logs/export/csv', [App\Http\Controllers\AdminLoginLogController::class, 'exportCsv'])->name('login-logs.export-csv');
            Route::get('/users', [App\Http\Controllers\AdminManagementController::class, 'users'])->name('users');
            Route::post('/users', [App\Http\Controllers\AdminManagementController::class, 'storeUser'])->name('users.store');
            Route::put('/users/{user}', [App\Http\Controllers\AdminManagementController::class, 'updateUser'])->name('users.update');
            Route::delete('/users/{user}', [App\Http\Controllers\AdminManagementController::class, 'destroyUser'])->name('users.destroy');
            // Invite client
            Route::get('/users/invite', [App\Http\Controllers\ClientInviteController::class, 'showInviteForm'])->name('users.invite');
            Route::post('/users/invite', [App\Http\Controllers\ClientInviteController::class, 'sendInvite'])->name('users.invite');
            // Permission Groups
            Route::get('/groups', [App\Http\Controllers\AdminGroupController::class, 'index'])->name('groups.index');
            Route::get('/groups/create', [App\Http\Controllers\AdminGroupController::class, 'create'])->name('groups.create');
            Route::post('/groups', [App\Http\Controllers\AdminGroupController::class, 'store'])->name('groups.store');
            Route::get('/groups/{group}/edit', [App\Http\Controllers\AdminGroupController::class, 'edit'])->name('groups.edit');
            Route::put('/groups/{group}', [App\Http\Controllers\AdminGroupController::class, 'update'])->name('groups.update');
            Route::delete('/groups/{group}', [App\Http\Controllers\AdminGroupController::class, 'destroy'])->name('groups.destroy');
            // Impersonate
            Route::get('/impersonate', [App\Http\Controllers\AdminImpersonateController::class, 'index'])->name('impersonate.index');
            Route::post('/impersonate/registrant/{registrant}', [App\Http\Controllers\AdminImpersonateController::class, 'impersonateRegistrant'])->name('impersonate.registrant');
            Route::post('/impersonate/{user}', [App\Http\Controllers\AdminImpersonateController::class, 'impersonate'])->name('impersonate.start');
            // Database Backup & Restore
            Route::get('/backup', [App\Http\Controllers\AdminBackupController::class, 'index'])->name('backup.index');
            Route::post('/backup', [App\Http\Controllers\AdminBackupController::class, 'store'])->name('backup.store');
            Route::get('/backup/{filename}/download', [App\Http\Controllers\AdminBackupController::class, 'download'])->name('backup.download');
            Route::delete('/backup/{filename}', [App\Http\Controllers\AdminBackupController::class, 'destroy'])->name('backup.destroy');
            Route::post('/backup/restore', [App\Http\Controllers\AdminBackupController::class, 'restore'])->name('backup.restore');
        });
    });
});
