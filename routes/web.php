<?php

use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectAttachmentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'authenticate']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('projects', ProjectController::class);
    Route::post('/projects/{project}/request',   [ProjectController::class, 'request'])->name('projects.request');
    Route::post('/projects/{project}/assign',    [ProjectController::class, 'assign'])->name('projects.assign');
    Route::post('/projects/{project}/accept',    [ProjectController::class, 'accept'])->name('projects.accept');
    Route::post('/projects/{project}/reject',    [ProjectController::class, 'reject'])->name('projects.reject');
    Route::post('/projects/{project}/submit',    [ProjectController::class, 'submit'])->name('projects.submit');
    Route::post('/projects/{project}/revise',    [ProjectController::class, 'revise'])->name('projects.revise');
    Route::post('/projects/{project}/resubmit',  [ProjectController::class, 'resubmit'])->name('projects.resubmit');
    Route::post('/projects/{project}/approve',   [ProjectController::class, 'approve'])->name('projects.approve');
    Route::get('/projects/{project}/logs',       [ProjectController::class, 'logs'])->name('projects.logs');
    Route::get('/projects/{project}/attachments',[ProjectController::class, 'attachments'])->name('projects.attachments');

    Route::post('/projects/{project}/request', [ProjectController::class, 'request'])->name('projects.request');
    Route::post('/projects/{project}/assign', [ProjectController::class, 'assign'])->name('projects.assign');
    Route::post('/projects/{project}/accept', [ProjectController::class, 'accept'])->name('projects.accept');
    Route::post('/projects/{project}/reject', [ProjectController::class, 'reject'])->name('projects.reject');
    Route::post('/projects/{project}/submit', [ProjectController::class, 'submit'])->name('projects.submit');
    Route::post('/projects/{project}/revise', [ProjectController::class, 'revise'])->name('projects.revise');
    Route::post('/projects/{project}/resubmit', [ProjectController::class, 'resubmit'])->name('projects.resubmit');
    Route::post('/projects/{project}/approve', [ProjectController::class, 'approve'])->name('projects.approve');

    Route::resource('users', UserController::class);
    Route::put('/users/{user}/password', [UserController::class, 'updatePassword'])
        ->name('users.password.update');

    Route::get('/freelancers/available', [ProjectController::class, 'availableFreelancers'])
        ->name('freelancers.available');

    Route::resource('skills', SkillController::class);


    Route::post('/logout', [AuthenticatedSessionController::class, 'logout'])->name('logout');
});
