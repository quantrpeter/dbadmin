<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DatabaseController;

// Login routes
Route::get('/', [DatabaseController::class, 'showLogin'])->name('login');
Route::post('/connect', [DatabaseController::class, 'connect'])->name('database.connect');

// Main database management routes
Route::get('/main', [DatabaseController::class, 'main'])->name('database.main');
Route::get('/database/{database}', [DatabaseController::class, 'selectDatabase'])->name('database.select');
Route::get('/table/{table}', [DatabaseController::class, 'showTable'])->name('database.table');

// Disconnect route
Route::post('/disconnect', [DatabaseController::class, 'disconnect'])->name('database.disconnect');
