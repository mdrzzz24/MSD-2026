<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\RegistrantAuthController;
use App\Http\Controllers\RegistrantDashboardController;
use App\Http\Controllers\AdminWorkshopController;

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
    $agendaItems = AgendaItem::ordered()->get();
    $timeSlots = \App\Models\TimeSlot::ordered()->get();
    $rooms = \App\Models\Room::ordered()->get();
    // Group items by time slot key
    $itemMap = [];
    foreach ($agendaItems as $item) {
        $key = $item->start_time . '-' . $item->end_time;
        $itemMap[$key][] = $item;
    }
    return view('home1', compact('agendaItems', 'timeSlots', 'rooms', 'itemMap'));
});
Route::get('/home2', function () {
    return view('home2');
});
Route::get('/home3', function () {
    return view('home3');
});

// ── Registration form submission (public) ──
Route::post('/register', [RegistrantAuthController::class, 'register'])->name('register.submit');
Route::get('/register/success', [RegistrantAuthController::class, 'success'])->name('register.success');

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

    // Workshop management
    Route::get('/workshops', [AdminWorkshopController::class, 'index'])->name('workshops.index');
    Route::get('/workshops/create', [AdminWorkshopController::class, 'create'])->name('workshops.create');
    Route::post('/workshops', [AdminWorkshopController::class, 'store'])->name('workshops.store');
    Route::get('/workshops/{workshop}/edit', [AdminWorkshopController::class, 'edit'])->name('workshops.edit');
    Route::put('/workshops/{workshop}', [AdminWorkshopController::class, 'update'])->name('workshops.update');
    Route::delete('/workshops/{workshop}', [AdminWorkshopController::class, 'destroy'])->name('workshops.destroy');
    Route::post('/workshops/{workshop}/toggle', [AdminWorkshopController::class, 'toggleRegistration'])->name('workshops.toggle');
    Route::get('/workshops/{workshop}/registrants', [AdminWorkshopController::class, 'registrants'])->name('workshops.registrants');

    // Agenda management
    Route::get('/agenda', [AdminAgendaController::class, 'index'])->name('agenda.index');
    Route::get('/agenda/create', [AdminAgendaController::class, 'create'])->name('agenda.create');
    Route::post('/agenda', [AdminAgendaController::class, 'store'])->name('agenda.store');
    Route::get('/agenda/{agendum}/edit', [AdminAgendaController::class, 'edit'])->name('agenda.edit');
    Route::put('/agenda/{agendum}', [AdminAgendaController::class, 'update'])->name('agenda.update');
    Route::delete('/agenda/{agendum}', [AdminAgendaController::class, 'destroy'])->name('agenda.destroy');
    Route::post('/agenda/{agendum}/merge', [AdminAgendaController::class, 'merge'])->name('agenda.merge');

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
});
