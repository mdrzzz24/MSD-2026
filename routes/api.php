<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

// Login — validates credentials against the `users` table (no token / no auth).
Route::post('/login', [ApiController::class, 'login']);

// Booth / agenda / registration endpoints (open, no authentication)
Route::get('/booths', [ApiController::class, 'booths']);
Route::get('/agenda', [ApiController::class, 'agenda']);
Route::post('/booths/{booth}/scan', [ApiController::class, 'boothScan']);
Route::post('/agenda/{agendum}/scan', [ApiController::class, 'agendaScan']);
Route::post('/agenda/{agendum}/trackout', [ApiController::class, 'agendaTrackOut']);
Route::post('/registration/scan', [ApiController::class, 'registrationScan']);
Route::post('/workshops/{workshop}/register', [ApiController::class, 'workshopRegister']);
// View attendees of a booth / agenda session
Route::get('/booths/{booth}/attendees', [ApiController::class, 'boothAttendees']);
Route::get('/agenda/{agendum}/attendees', [ApiController::class, 'agendaAttendees']);
// Offline scan sync — app queues scans while offline, uploads here when back online.
Route::post('/sync/scans', [ApiController::class, 'syncScans']);
