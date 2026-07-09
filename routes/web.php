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
use App\Models\AgendaItem;

// Public routes
Route::get('/', function () {
    return view('home');
});
Route::get('/home1', function () {
    $agendaItems = AgendaItem::ordered()->with('speakers')->get();
    $timeSlots = \App\Models\TimeSlot::ordered()->get();
    $rooms = \App\Models\Room::ordered()->get();
    // Group items by time slot key
    $itemMap = [];
    foreach ($agendaItems as $item) {
        $key = $item->start_time . '-' . $item->end_time;
        $itemMap[$key][] = $item;
    }
    $registrationForcedOpen = \Illuminate\Support\Facades\Cache::get('registration_forced_open', false);
    $workshops = \App\Models\Workshop::withCount(['registrants as approved_count' => function ($q) {
        $q->where('registrant_workshop.status', 'approved');
    }])->orderBy('date')->orderBy('start_time')->get();
    return view('home1', compact('agendaItems', 'timeSlots', 'rooms', 'itemMap', 'registrationForcedOpen', 'workshops'));
})->name('home1');
Route::get('/home2', function () {
    return view('home2');
});
Route::get('/home3', function () {
    return view('home3');
});

// ── Registration form submission (public) ──
Route::post('/register', [RegistrantAuthController::class, 'register'])->name('register.submit');
Route::get('/register/success', [RegistrantAuthController::class, 'success'])->name('register.success');

// ── QR Scan (public) ──
Route::get('/qr/{token}', [App\Http\Controllers\QrScanController::class, 'scan'])->name('registrant.qr-scan');
Route::post('/qr/{token}/checkin', [App\Http\Controllers\QrScanController::class, 'checkin'])->name('registrant.qr-checkin');
Route::get('/qr-view/{token}', [App\Http\Controllers\QrScanController::class, 'share'])->name('registrant.qr-share');

// ── Admin auth routes (unified — handles Admin & Registrant via role selector) ──
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ── Registrant auth (redirect to unified login) ──
Route::get('/registrant/login', function () {
    return redirect()->route('login');
})->name('registrant.login');
Route::post('/registrant/logout', [RegistrantAuthController::class, 'logout'])->name('registrant.logout');

// ── Registrant dashboard (protected) ──
Route::middleware('auth:registrant')->prefix('registrant')->name('registrant.')->group(function () {
    Route::get('/dashboard', [RegistrantDashboardController::class, 'dashboard'])->name('dashboard');
    Route::post('/workshops/{workshop}/register', [RegistrantDashboardController::class, 'registerWorkshop'])->name('workshop.register');
    Route::post('/workshops/{workshop}/unregister', [RegistrantDashboardController::class, 'unregisterWorkshop'])->name('workshop.unregister');
    // Agenda item registration
    Route::post('/agenda/{agendaItem}/register', [RegistrantDashboardController::class, 'registerAgenda'])->name('agenda.register');
    Route::post('/agenda/{agendaItem}/unregister', [RegistrantDashboardController::class, 'unregisterAgenda'])->name('agenda.unregister');
});

// ── Admin routes (protected by admin middleware) ──
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Registrant management
    Route::get('/registrants', [AdminController::class, 'index'])->name('registrants.index');
    Route::get('/registrants/{registrant}', [AdminController::class, 'show'])->name('registrants.show');
    Route::get('/registrants/{registrant}/edit', [AdminController::class, 'edit'])->name('registrants.edit');
    Route::put('/registrants/{registrant}', [AdminController::class, 'update'])->name('registrants.update');
    Route::delete('/registrants/{registrant}', [AdminController::class, 'destroy'])->name('registrants.destroy');
    Route::post('/registrants/{registrant}/approve', [AdminController::class, 'approve'])->name('registrants.approve');
    Route::post('/registrants/{registrant}/reject', [AdminController::class, 'reject'])->name('registrants.reject');
    Route::post('/registrants/{registrant}/resend-credentials', [AdminController::class, 'resendCredentials'])->name('registrants.resend-credentials');
    Route::post('/registrants/bulk-approve', [AdminController::class, 'bulkApprove'])->name('registrants.bulk-approve');
    Route::post('/registrants/bulk-reject', [AdminController::class, 'bulkReject'])->name('registrants.bulk-reject');
    Route::get('/registrants/export/csv', [AdminController::class, 'exportCsv'])->name('registrants.export-csv');

    // ── Super Admin only sections ──
    Route::middleware('super_admin')->group(function () {

    // Registration Form Toggle
    Route::post('/toggle-registration', [AdminController::class, 'toggleRegistration'])->name('toggle-registration');

    // Email Templates
    Route::get('/templates', [EmailTemplateController::class, 'index'])->name('templates.index');
    Route::get('/templates/upload', [EmailTemplateController::class, 'uploadForm'])->name('templates.upload');
    Route::post('/templates/upload', [EmailTemplateController::class, 'upload'])->name('templates.upload.store');
    Route::get('/templates/create', [EmailTemplateController::class, 'create'])->name('templates.create');
    Route::post('/templates', [EmailTemplateController::class, 'store'])->name('templates.store');
    Route::get('/templates/{template}/edit', [EmailTemplateController::class, 'edit'])->name('templates.edit');
    Route::put('/templates/{template}', [EmailTemplateController::class, 'update'])->name('templates.update');
    Route::delete('/templates/{template}', [EmailTemplateController::class, 'destroy'])->name('templates.destroy');
    Route::post('/templates/{template}/toggle', [EmailTemplateController::class, 'toggleActive'])->name('templates.toggle');

    // Workshop CRUD (super_admin only)
    Route::get('/workshops/create', [AdminWorkshopController::class, 'create'])->name('workshops.create');
    Route::post('/workshops', [AdminWorkshopController::class, 'store'])->name('workshops.store');
    Route::get('/workshops/{workshop}/edit', [AdminWorkshopController::class, 'edit'])->name('workshops.edit');
    Route::put('/workshops/{workshop}', [AdminWorkshopController::class, 'update'])->name('workshops.update');
    Route::delete('/workshops/{workshop}', [AdminWorkshopController::class, 'destroy'])->name('workshops.destroy');
    Route::post('/workshops/{workshop}/toggle', [AdminWorkshopController::class, 'toggleRegistration'])->name('workshops.toggle');
    // Agenda management
    Route::get('/agenda', [AdminAgendaController::class, 'index'])->name('agenda.index');
    Route::get('/agenda/create', [AdminAgendaController::class, 'create'])->name('agenda.create');
    Route::post('/agenda', [AdminAgendaController::class, 'store'])->name('agenda.store');
    Route::get('/agenda/{agendum}/edit', [AdminAgendaController::class, 'edit'])->name('agenda.edit');
    Route::put('/agenda/{agendum}', [AdminAgendaController::class, 'update'])->name('agenda.update');
    Route::delete('/agenda/{agendum}', [AdminAgendaController::class, 'destroy'])->name('agenda.destroy');
    Route::post('/agenda/{agendum}/merge', [AdminAgendaController::class, 'merge'])->name('agenda.merge');
    Route::post('/agenda/{agendum}/toggle-registration', [AdminAgendaController::class, 'toggleRegistration'])->name('agenda.toggle-registration');

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
    // Track registrants approve/reject (super_admin + admin only)
    Route::post('/tracks/{track}/registrants/{registrant}/approve', [AdminTrackController::class, 'approveRegistrant'])->name('tracks.registrants.approve');
    Route::post('/tracks/{track}/registrants/{registrant}/reject', [AdminTrackController::class, 'rejectRegistrant'])->name('tracks.registrants.reject');

    });

    // ── Workshop & Track Viewing — accessible by all admin roles (including client) ──
    Route::get('/workshops', [AdminWorkshopController::class, 'index'])->name('workshops.index');
    Route::get('/workshops/{workshop}/registrants', [AdminWorkshopController::class, 'registrants'])->name('workshops.registrants');
    Route::get('/tracks', [AdminTrackController::class, 'index'])->name('tracks.index');
    Route::get('/tracks/{track}/registrants', [AdminTrackController::class, 'registrants'])->name('tracks.registrants');

    // ── Workshop Registrants Management (admin + super_admin) ──
    Route::get('/workshop-registrants', [AdminWorkshopController::class, 'workshopRegistrants'])->name('workshop-registrants.index');
    Route::post('/workshops/{workshop}/registrants/{registrant}/approve', [AdminWorkshopController::class, 'approveRegistrant'])->name('workshops.registrants.approve');
    Route::post('/workshops/{workshop}/registrants/{registrant}/reject', [AdminWorkshopController::class, 'rejectRegistrant'])->name('workshops.registrants.reject');

    // ── Agenda Registrants — accessible by all admin roles ──
    Route::get('/agenda-registrants', [AdminAgendaController::class, 'registrantsIndex'])->name('agenda-registrants.index');
    Route::get('/agenda-registrants/{agendum}', [AdminAgendaController::class, 'registrantsDetail'])->name('agenda-registrants.detail');
    Route::post('/agenda-registrants/{agendum}/registrants/{registrant}/approve', [AdminAgendaController::class, 'registrantsApprove'])->name('agenda-registrants.approve');
    Route::post('/agenda-registrants/{agendum}/registrants/{registrant}/reject', [AdminAgendaController::class, 'registrantsReject'])->name('agenda-registrants.reject');

    // ── Management (UTM - all admins, scoped) ──
    Route::prefix('management')->name('management.')->group(function () {
        Route::get('/utm-sources', [App\Http\Controllers\AdminManagementController::class, 'utmSources'])->name('utm');
        Route::post('/utm-links', [App\Http\Controllers\AdminManagementController::class, 'storeUtmLink'])->name('utm-links.store');
        Route::put('/utm-links/{utmLink}', [App\Http\Controllers\AdminManagementController::class, 'updateUtmLink'])->name('utm-links.update');
        Route::delete('/utm-links/{utmLink}', [App\Http\Controllers\AdminManagementController::class, 'destroyUtmLink'])->name('utm-links.destroy');

        // QR Codes — all admins can view
        Route::get('/qr-codes', [App\Http\Controllers\AdminManagementController::class, 'qrCodes'])->name('qr');

        // Check-in, Users — super_admin only
        Route::middleware('super_admin')->group(function () {
            Route::get('/checkin-log', [App\Http\Controllers\AdminManagementController::class, 'checkinLog'])->name('checkin');
            Route::get('/users', [App\Http\Controllers\AdminManagementController::class, 'users'])->name('users');
            Route::post('/users', [App\Http\Controllers\AdminManagementController::class, 'storeUser'])->name('users.store');
            Route::put('/users/{user}', [App\Http\Controllers\AdminManagementController::class, 'updateUser'])->name('users.update');
            Route::delete('/users/{user}', [App\Http\Controllers\AdminManagementController::class, 'destroyUser'])->name('users.destroy');
        });
    });
});
