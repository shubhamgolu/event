<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\User\EventController as UserEventController;
use App\Http\Controllers\Admin\SurveyController;
use App\Http\Controllers\Admin\CheckinController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentication Routes
Auth::routes();

// Protected Routes (all users)
Route::middleware(['auth'])->group(function () {
    // User Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // User Event Routes
    Route::prefix('events')->name('user.events.')->group(function () {
        Route::get('/', [UserEventController::class, 'index'])->name('index');
        Route::get('/my-events', [UserEventController::class, 'myEvents'])->name('my-events');
        Route::get('/{event}', [UserEventController::class, 'show'])->name('show');
        Route::post('/{event}/register', [UserEventController::class, 'register'])->name('register');
        Route::delete('/{event}/cancel', [UserEventController::class, 'cancelRegistration'])->name('cancel-registration');
    });
    
    // Admin Routes - TEMPORARILY WITHOUT MIDDLEWARE
    Route::prefix('admin')->name('admin.')->group(function () {
        // Admin Dashboard - Add manual check
        Route::get('/dashboard', function () {
            if (!auth()->check() || !auth()->user()->isAdmin()) {
                abort(403, 'Admin access required');
            }
            return app(AdminDashboardController::class)->index();
        })->name('dashboard');
        
        // Admin Event Routes - Add manual check in constructor
        Route::resource('events', AdminEventController::class)->except(['show']);
        Route::get('events/{event}', [AdminEventController::class, 'show'])->name('events.show');
        Route::patch('events/{event}/toggle-status', [AdminEventController::class, 'toggleStatus'])->name('events.toggle-status');
        

        // Admin Survey Routes
Route::prefix('surveys')->name('surveys.')->group(function () {
    Route::get('/', [SurveyController::class, 'index'])->name('index');
    Route::get('/create', [SurveyController::class, 'create'])->name('create');
    Route::get('/create/event/{event}', [SurveyController::class, 'createForEvent'])->name('create.for-event');
    Route::post('/', [SurveyController::class, 'store'])->name('store');
    Route::get('/{survey}', [SurveyController::class, 'show'])->name('show');
    Route::get('/{survey}/edit', [SurveyController::class, 'edit'])->name('edit');
    Route::put('/{survey}', [SurveyController::class, 'update'])->name('update');
    Route::delete('/{survey}', [SurveyController::class, 'destroy'])->name('destroy');
    Route::get('/{survey}/preview', [SurveyController::class, 'preview'])->name('preview');
    Route::get('/{survey}/clone', [SurveyController::class, 'clone'])->name('clone');
    Route::post('/{survey}/clone', [SurveyController::class, 'storeClone'])->name('store.clone');
    Route::patch('/{survey}/toggle-status', [SurveyController::class, 'toggleStatus'])->name('toggle-status');
    // Add this PUBLIC survey route
Route::get('/survey/{token}', function($token) {
    // Simple response - you can improve this later
    return response()->json([
        'message' => 'Survey endpoint',
        'token' => $token,
        'status' => 'active'
    ]);
})->name('survey.show');
});

// Admin Check-in Routes
Route::prefix('checkin')->name('checkin.')->group(function () {
    Route::get('/event/{event}', [CheckinController::class, 'show'])->name('show');
    Route::post('/participant/{participant}/checkin', [CheckinController::class, 'checkin'])->name('checkin');
    Route::post('/participant/{participant}/checkout', [CheckinController::class, 'checkout'])->name('checkout');
    Route::post('/participant/{participant}/send-survey', [CheckinController::class, 'sendSurvey'])->name('send-survey'); // ADD THIS
    Route::post('/event/{event}/bulk', [CheckinController::class, 'bulkCheckin'])->name('bulk');
    Route::get('/event/{event}/search', [CheckinController::class, 'search'])->name('search');

});
        
    });
});