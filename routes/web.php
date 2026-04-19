<?php

use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
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

    Route::post('/projects/{project}/request', [ProjectController::class, 'request'])->name('projects.request');
    Route::post('/projects/{project}/assign', [ProjectController::class, 'assign'])->name('projects.assign');
    Route::post('/projects/{project}/accept', [ProjectController::class, 'accept'])->name('projects.accept');
    Route::post('/projects/{project}/reject', [ProjectController::class, 'reject'])->name('projects.reject');
    Route::post('/projects/{project}/submit', [ProjectController::class, 'submit'])->name('projects.submit');
    Route::post('/projects/{project}/revise', [ProjectController::class, 'revise'])->name('projects.revise');
    Route::post('/projects/{project}/resubmit', [ProjectController::class, 'resubmit'])->name('projects.resubmit');
    Route::post('/projects/{project}/approve', [ProjectController::class, 'approve'])->name('projects.approve');

    Route::get('/projects/{project}/logs', function ($projectId) {
        return \App\Models\Project::with('projectlogs.actor')->findOrFail($projectId);
    })->name('projects.logs');

    Route::get('/projects/{project}/attachments', function ($projectId) {
        return \App\Models\Project::with('attachments')->findOrFail($projectId);
    })->name('projects.attachments');

    Route::delete('/attachments/{attachment}', function ($attachmentId) {
        $attachment = \App\Models\ProjectAttachment::findOrFail($attachmentId);
        $attachment->delete();
        return back()->with('success', 'Attachment deleted');
    })->name('attachments.destroy');

    Route::post('/logout', [AuthenticatedSessionController::class, 'logout'])->name('logout');
});
