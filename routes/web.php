<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DatabaseController;

// Login routes
Route::get('/', [DatabaseController::class, 'showLogin'])->name('login');
Route::post('/connect', [DatabaseController::class, 'connect'])->name('database.connect');

// Main database management routes
Route::get('/main', [DatabaseController::class, 'main'])->name('database.main');
Route::get('/database/{database}', [DatabaseController::class, 'selectDatabase'])->name('database.select');
Route::post('/database/deselect', [DatabaseController::class, 'deselectDatabase'])->name('database.deselect');
Route::get('/table/{table}', [DatabaseController::class, 'showTable'])->name('database.table');

// Database CRUD
Route::post('/database/create', [DatabaseController::class, 'createDatabase'])->name('database.create');
Route::post('/database/{database}/drop', [DatabaseController::class, 'dropDatabase'])->name('database.drop');

// Table CRUD
Route::post('/table/create', [DatabaseController::class, 'createTable'])->name('table.create');
Route::post('/table/{table}/rename', [DatabaseController::class, 'renameTable'])->name('table.rename');
Route::post('/table/{table}/drop', [DatabaseController::class, 'dropTable'])->name('table.drop');
Route::post('/table/{table}/add-column', [DatabaseController::class, 'addColumn'])->name('table.addColumn');
Route::post('/table/{table}/modify-column', [DatabaseController::class, 'modifyColumn'])->name('table.modifyColumn');
Route::post('/table/{table}/drop-column', [DatabaseController::class, 'dropColumn'])->name('table.dropColumn');

// User Management
Route::get('/users', [DatabaseController::class, 'listUsers'])->name('users.list');
Route::post('/users/create', [DatabaseController::class, 'createUser'])->name('users.create');
Route::post('/users/{user}/update', [DatabaseController::class, 'updateUser'])->name('users.update');
Route::post('/users/{user}/delete', [DatabaseController::class, 'deleteUser'])->name('users.delete');
Route::post('/users/{user}/permissions', [DatabaseController::class, 'updatePermissions'])->name('users.permissions');

// Disconnect route
Route::post('/disconnect', [DatabaseController::class, 'disconnect'])->name('database.disconnect');
