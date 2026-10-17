<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CmsDashboardController;
use App\Http\Controllers\CmsGateController;
use App\Http\Controllers\CmsRundownController;
use App\Http\Controllers\CmsStandController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HealthCheckController;
use App\Http\Controllers\PosAuthController;
use App\Http\Controllers\PosGatewayController;
use App\Http\Controllers\PosterGeneratorController;
use App\Http\Controllers\PosTerminalController;
use App\Http\Controllers\PublicEventController;
use App\Http\Controllers\PublicLeaderboardController;
use App\Http\Controllers\SpkEvaluationController;
use App\Http\Controllers\StandAssignmentController;
use App\Http\Controllers\StandMenuController;
use App\Models\Event;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

Route::get('/', function () {
    $events = Event::withCount([
        'stands',
        'stands as available_stands_count' => fn ($q) => $q->whereNull('umkm_id'),
        'attendees',
    ])
        ->with(['stands' => fn ($q) => $q->with('umkmProfile')->orderBy('nomor_stand')])
        ->latest()
        ->take(6)
        ->get();

    return Inertia::render('Welcome', [
        'status' => 'Sistem PATEN Siap',
        'version' => '1.0.0',
        'events' => $events,
    ]);
});

Route::get('/health', [HealthCheckController::class, 'index'])->name('health.check');

// Stage 2: Event Generation & Public Pages
Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
Route::post('/events', [EventController::class, 'store'])->name('events.store');

Route::get('/e/{slug}', [PublicEventController::class, 'show'])->name('events.public.show');
Route::post('/e/{slug}/register-attendance', [PublicEventController::class, 'registerAttendance'])
    ->middleware('throttle:attendance')
    ->name('events.public.register');

Route::get('/events/{slug}/poster-export', [PosterGeneratorController::class, 'export'])->name('events.poster.export');

// Stage 3: Protected CMS Routes (middleware cms.token)
Route::middleware('cms.token')->group(function () {
    Route::get('/cms/{slug}', [CmsDashboardController::class, 'show'])->name('cms.show');
    Route::post('/cms/{slug}/rundowns', [CmsRundownController::class, 'store'])->name('cms.rundowns.store');
    Route::put('/cms/{slug}/rundowns/{id}', [CmsRundownController::class, 'update'])->name('cms.rundowns.update');
    Route::delete('/cms/{slug}/rundowns/{id}', [CmsRundownController::class, 'destroy'])->name('cms.rundowns.destroy');
    Route::post('/cms/{slug}/stands', [CmsStandController::class, 'provision'])->name('cms.stands.provision');
    Route::post('/cms/{slug}/check-in', [CmsGateController::class, 'checkIn'])->name('cms.gate.checkin');
    Route::post('/cms/{slug}/draw-doorprize', [CmsGateController::class, 'drawDoorprize'])->name('cms.gate.doorprize');
    Route::post('/cms/{slug}/evaluate-spk', [SpkEvaluationController::class, 'evaluate'])->name('cms.spk.evaluate');
    Route::get('/cms/{slug}/evaluations', [SpkEvaluationController::class, 'index'])->name('cms.spk.evaluations');
});

// Stage 5: Public Leaderboard (Strict Financial Data Masking)
Route::get('/e/{slug}/leaderboard', [PublicLeaderboardController::class, 'index'])->name('events.public.leaderboard');

// Stage 4: Stand Assignment & UMKM Profiling
Route::get('/stands/{stand_id}/assign', [StandAssignmentController::class, 'create'])->name('stands.assign.create');
Route::post('/stands/{stand_id}/assign', [StandAssignmentController::class, 'assign'])->name('stands.assign.store');

// Centralized POS Gateway for Merchants
Route::get('/pos', [PosGatewayController::class, 'index'])->name('pos.gateway');
Route::post('/pos/authenticate', [PosGatewayController::class, 'authenticate'])
    ->middleware('throttle:15,1')
    ->name('pos.gateway.authenticate');

// Stage 4: POS Auth & Menu Export
Route::get('/pos/{stand_id}/login', [PosAuthController::class, 'showLogin'])->name('pos.login');
Route::post('/pos/{stand_id}/login-pin', [PosAuthController::class, 'login'])
    ->middleware('throttle:10,1')
    ->name('pos.login.submit');
Route::get('/pos/{stand_id}/export-menu', [StandMenuController::class, 'export'])->name('pos.menus.export');

// Stage 4: Protected POS Routes (middleware pos.pin)
Route::middleware('pos.pin')->group(function () {
    Route::get('/pos/{stand_id}', [PosTerminalController::class, 'index'])->name('pos.terminal');
    Route::post('/pos/{stand_id}/transactions', [PosTerminalController::class, 'recordTransaction'])
        ->middleware('throttle:60,1')
        ->name('pos.transactions.store');
    Route::post('/pos/{stand_id}/menus', [StandMenuController::class, 'store'])->name('pos.menus.store');
    Route::put('/pos/{stand_id}/menus/{menu_id}', [StandMenuController::class, 'update'])->name('pos.menus.update');
    Route::delete('/pos/{stand_id}/menus/{menu_id}', [StandMenuController::class, 'destroy'])->name('pos.menus.destroy');

    // Diagnostic/Testing endpoint from Stage 1
    Route::get('/pos/{stand_id}/test', function () {
        return response()->json([
            'success' => true,
            'message' => 'POS Authorized Access',
            'stand' => request()->attributes->get('stand'),
        ]);
    });
});
