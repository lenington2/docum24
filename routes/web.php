<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\PageVisitController;
use Illuminate\Support\Facades\Auth;

// ─── Landing page ──────────────────────────────
Route::get('/', function () {
    PageVisitController::record(request());
    return view('welcome');
});

// ─── Contador público de usuarios ──────────────
Route::get('/api/user-count', function () {
    $count = \App\Models\User::count() * 7;
    return response()->json(['count' => $count]);
});

// ─── Stripe webhook (sin auth) ─────────────────
Route::post('/stripe/webhook', [App\Http\Controllers\StripeController::class, 'webhook']);

// ─── Business prompt preview (sin auth) ────────
Route::post('/user/business-prompt-preview', [App\Http\Controllers\ChatController::class, 'generarBusinessPromptPublic']);

// ─── Email verification ────────────────────────
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/dashboard');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// ─── Rutas autenticadas (solo las que NO son API) ─
Route::middleware(['auth'])->group(function () {
    // Dashboard → sirve el SPA de React
    Route::get('/dashboard', function () {
        return redirect('/app');
    })->name('dashboard');

    // Stripe checkout
    Route::get('/checkout/{plan}', [App\Http\Controllers\StripeController::class, 'checkout'])->name('checkout');
    Route::get('/checkout/success', [App\Http\Controllers\StripeController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel', [App\Http\Controllers\StripeController::class, 'cancel'])->name('checkout.cancel');

    // Aceptar invitación de equipo
    Route::get('/teams/invitations/{invitation}/accept', [TeamController::class, 'acceptInvitation'])->name('team.invitation.accept');
});

// ─── Preview público de documentos ─────────────
Route::get('/preview-public', [DocumentoController::class, 'previewPublic'])->name('preview.public');

// ─── Switch a equipo personal ──────────────────
Route::post('/teams/switch/personal', function() {
    Auth::user()->update(['current_team_id' => null]);
    return response()->json(['success' => true]);
});
