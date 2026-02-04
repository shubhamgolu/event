<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\User\EventController as UserEventController;
use App\Http\Controllers\Admin\SurveyController;
use App\Http\Controllers\Admin\CheckinController;
use App\Http\Controllers\PublicSurveyController;
use App\Http\Controllers\Admin\ReportController;


Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentication Routes
Auth::routes();

Route::get('/survey/{token}', [PublicSurveyController::class, 'show'])->name('survey.show');

// Also add these for full functionality:
Route::post('/survey/{token}/submit', [PublicSurveyController::class, 'submit'])->name('survey.submit');
Route::get('/survey/{token}/success', [PublicSurveyController::class, 'success'])->name('survey.success');

// ==================== EMAIL TESTING ROUTES (PUBLIC - ADD HERE) ====================

// Test 1: Debug current mail configuration
Route::get('/debug-mail-config', function() {
    echo "<h2>🔧 Current Mail Configuration</h2>";
    echo "<pre>";
    
    // Check .env values
    echo "=== .env values ===\n";
    echo "MAIL_MAILER: " . env('MAIL_MAILER') . "\n";
    echo "MAIL_HOST: " . env('MAIL_HOST') . "\n";
    echo "MAIL_PORT: " . env('MAIL_PORT') . "\n";
    echo "MAIL_USERNAME: " . env('MAIL_USERNAME') . "\n";
    echo "MAIL_PASSWORD: " . (env('MAIL_PASSWORD') ? 'SET' : 'NOT SET') . "\n";
    echo "MAIL_ENCRYPTION: " . env('MAIL_ENCRYPTION') . "\n";
    echo "MAIL_FROM_ADDRESS: " . env('MAIL_FROM_ADDRESS') . "\n";
    echo "MAIL_FROM_NAME: " . env('MAIL_FROM_NAME') . "\n\n";
    
    // Check config values
    echo "=== Config values ===\n";
    $config = config('mail');
    echo "Default Mailer: " . $config['default'] . "\n";
    echo "From Address: " . $config['from']['address'] . "\n";
    echo "From Name: " . $config['from']['name'] . "\n\n";
    
    echo "</pre>";
    
    // Check for .env duplicates
    if (file_exists('.env')) {
        $envContent = file_get_contents('.env');
        $lines = explode("\n", $envContent);
        $mailLines = array_filter($lines, fn($line) => str_starts_with(trim($line), 'MAIL_'));
        
        echo "<h3>📋 MAIL lines in .env:</h3>";
        echo "<pre>";
        echo implode("\n", $mailLines);
        echo "</pre>";
    }
    
    echo '<br><a href="/force-real-email" style="padding:10px;background:green;color:white;">🚀 Test Real Email</a>';
});

// Test 2: Force send real email
Route::get('/force-real-email', function() {
    echo "<h2>🚀 FORCING REAL EMAIL SEND</h2>";
    
    try {
        // Manually configure Gmail SMTP
        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp' => [
                'transport' => 'smtp',
                'host' => 'smtp.gmail.com',
                'port' => 587,
                'encryption' => 'tls',
                'username' => '',
                'password' => '',
                'timeout' => null,
                'auth_mode' => null,
            ],
            'mail.from' => [
                'address' => '',
                'name' => 'Event System',
            ],
        ]);
        
        // Rebind mailer with new config
        app()->forgetInstance('mail.manager');
        app()->forgetInstance('mailer');
        
        // Send test email
        \Mail::raw('🎉 SUCCESS! Your Laravel email is now working with Gmail SMTP!', function($message) {
            $message->to('shubham.dubey812@gmail.com')
                    ->subject('✅ GMAIL SMTP WORKING - Event System')
                    ->from('rahulsharma81229@gmail.com', 'Event System');
        });
        
        echo "<h1 style='color: green;'>✅ EMAIL SENT VIA GMAIL SMTP!</h1>";
        echo "<p>Check: <strong>shubham.dubey812@gmail.com</strong></p>";
        echo "<p>Also check SPAM folder if not in inbox.</p>";
        
    } catch (\Exception $e) {
        echo "<h1 style='color: red;'>❌ SMTP ERROR</h1>";
        echo "<pre>Error: " . $e->getMessage() . "</pre>";
        
        echo "<h3>🔧 Solutions:</h3>";
        echo "<ol>";
        echo "<li><strong>Get NEW App Password:</strong> <a href='https://myaccount.google.com/apppasswords' target='_blank'>https://myaccount.google.com/apppasswords</a></li>";
        echo "<li>Select: Mail → Other → Name: 'Laravel Event'</li>";
        echo "<li>Copy 16-char password (no spaces)</li>";
        echo "<li>Update .env with new password</li>";
        echo "</ol>";
        
        echo "<h3>📝 Update .env with:</h3>";
        echo "<pre>";
        echo "MAIL_MAILER=smtp\n";
        echo "MAIL_HOST=smtp.gmail.com\n";
        echo "MAIL_PORT=587\n";
        echo "MAIL_USERNAME=rahulsharma81229@gmail.com\n";
        echo "MAIL_PASSWORD=NEW_16_CHAR_PASSWORD_HERE\n";
        echo "MAIL_ENCRYPTION=tls\n";
        echo "MAIL_FROM_ADDRESS=\"rahulsharma81229@gmail.com\"\n";
        echo "MAIL_FROM_NAME=\"Event System\"\n";
        echo "</pre>";
    }
});

// Test 3: Simple test route
Route::get('/test-email-quick', function() {
    try {
        \Mail::raw('Quick test email', function($message) {
            $message->to('shubham.dubey812@gmail.com')
                    ->subject('Quick Test')
                    ->from('rahulsharma81229@gmail.com', 'Event System');
        });
        return 'Quick test email sent!';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

// Test 4: Check if routes are working
Route::get('/test-route', function() {
    return '✅ Routes are working!';
});

// ==================== END EMAIL TESTING ROUTES ====================

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
            Route::post('/participant/{participant}/send-survey', [CheckinController::class, 'sendSurvey'])->name('send-survey');
            Route::post('/event/{event}/bulk', [CheckinController::class, 'bulkCheckin'])->name('bulk');
            Route::get('/event/{event}/search', [CheckinController::class, 'search'])->name('search');
        });

        // Move these test routes OUTSIDE or keep simple ones
        Route::get('/test-email-view/{participantId}', function($participantId) {
            try {
                $participant = \App\Models\Participant::with(['user', 'event'])->find($participantId);
                
                if (!$participant) {
                    return "Participant not found. Create one first.";
                }
                
                // Generate token
                $token = \Str::random(60);
                $surveyUrl = url("/survey/{$token}");
                
                // Send test email
                \Mail::to($participant->user->email)->send(new \App\Mail\SurveyInvitationMail(
                    $participant,
                    $surveyUrl
                ));
                
                return response()->json([
                    'success' => true,
                    'message' => 'Email sent!',
                    'to' => $participant->user->email,
                    'participant_id' => $participant->id,
                    'check_logs' => 'Check storage/logs/laravel.log for details'
                ]);
                
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ], 500);
            }
        });
        
        Route::get('/test-email/{participantId}', function($participantId) {
            $participant = \App\Models\Participant::find($participantId);
            if ($participant) {
                $result = $participant->sendSurvey();
                return $result ? 'Email sent!' : 'Failed to send email';
            }
            return 'Participant not found';
        });

        // Admin Report Routes
Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('/event-registrations', [ReportController::class, 'eventRegistrations'])->name('event-registrations');
    Route::get('/survey-responses', [ReportController::class, 'surveyResponses'])->name('survey-responses');
    Route::get('/certificates', [ReportController::class, 'certificates'])->name('certificates');
    Route::get('/event/{event}', [ReportController::class, 'eventDetail'])->name('event-detail');
    Route::get('/export/event-registrations', [ReportController::class, 'exportEventRegistrations'])->name('export.event-registrations');
    Route::get('/statistics', [ReportController::class, 'getStatistics'])->name('statistics');
});




    });
});
