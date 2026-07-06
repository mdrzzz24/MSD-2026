<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});
Route::get('/home1', function () {
    return view('home1');
});

Route::get('/home2', function () {
    return view('home2');
});
