<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::get('/booths', [ApiController::class, 'booths']);
Route::get('/agenda', [ApiController::class, 'agenda']);

Route::post('/booths/{booth}/scan', [ApiController::class, 'boothScan']);
Route::post('/agenda/{agendum}/scan', [ApiController::class, 'agendaScan']);
