<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\TipologiaController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\ClaudeController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ActividadController;
use App\Http\Controllers\ConversacionController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ReporteChatController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\NotificaController;
use App\Http\Controllers\UrlFavoritaController;
use App\Http\Controllers\SupportChatController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\PageVisitController;

Route::get('/', function () {
    PageVisitController::record(request());
    return view('welcome');
});

Route::get('/api/user-count', function () {
    $count = \App\Models\User::count() * 7;

    return response()->json(['count' => $count]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
   Route::get('/dashboard', function () {
        $empresa = auth()->user()->empresa;
        $tieneEmpresa = !empty($empresa?->nombre);
        return view('dashboard', compact('tieneEmpresa'));
    })->name('dashboard');
});

Route::post('/user/business-prompt-preview', [ChatController::class, 'generarBusinessPromptPublic']);
Route::post('/stripe/webhook', [App\Http\Controllers\StripeController::class, 'webhook']);

//E-mail verifications
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

Route::middleware(['auth'])->group(function () {
    //planes tokens
    Route::get('/user/tokens', function () {
        $service = new \App\Services\TokenService();
        return response()->json($service->getHeaderData(Auth::user()));
    });
    //proyetos
    Route::get('/proyectos', [ProyectoController::class, 'index']);
    Route::post('/proyectos', [ProyectoController::class, 'store']);
    Route::put('/proyectos/{proyecto}', [ProyectoController::class, 'update']);
    Route::delete('/proyectos/{proyecto}', [ProyectoController::class, 'destroy']);
    //categoria
    Route::get('/proyectos/{proyecto}/categorias', [CategoriaController::class, 'index']);
    Route::post('/categorias', [CategoriaController::class, 'store']);
    Route::put('/categorias/{categoria}', [CategoriaController::class, 'update']);
    Route::delete('/categorias/{categoria}', [CategoriaController::class, 'destroy']);
    // tipologia
    Route::get('/categorias/{categoria}/tipologias', [TipologiaController::class, 'index']);
    Route::post('/tipologias', [TipologiaController::class, 'store']);
    Route::put('/tipologias/{tipologia}', [TipologiaController::class, 'update']);
    Route::delete('/tipologias/{tipologia}', [TipologiaController::class, 'destroy']);
    // Documentos - rutas POST sin parámetros PRIMERO
    Route::post('/documentos', [DocumentoController::class, 'store']);
    Route::post('/documentos/check-size', [DocumentoController::class, 'checkSize']);
    Route::post('/documentos/download-multiple', [DocumentoController::class, 'downloadMultiple']);
    Route::post('/chat/con-documenti', [DocumentoController::class, 'chatConDocumenti']);

    // Rutas con {documento} DESPUÉS
    Route::get('/proyectos/{proyecto}/dataroom', [DocumentoController::class, 'porProyecto']);
    Route::delete('/documentos/{documento}', [DocumentoController::class, 'destroy']);
    Route::get('/documentos/{documento}/download', [DocumentoController::class, 'download']);
    Route::get('/documentos/{documento}/info', [DocumentoController::class, 'info']);
    Route::put('/documentos/{documento}', [DocumentoController::class, 'update']);
    Route::get('/documentos/{documento}/preview-token', [DocumentoController::class, 'previewToken']);
    Route::get('/preview-public', [DocumentoController::class, 'previewPublic'])->name('preview.public');
    //file-view
    Route::get('/documentos/{documento}/info',     [DocumentoController::class, 'info']);
    Route::get('/documentos/{documento}/preview',  [DocumentoController::class, 'preview']);
    Route::put('/documentos/{documento}',          [DocumentoController::class, 'update']);
    //AI UPLOAD FILE
    Route::post('/claude/analizar', [ClaudeController::class, 'analizarYGuardar']);
    //CHAT AI
    Route::post('/chat/send', [ChatController::class, 'send']);
    //actividades
    Route::get('/actividades', [ActividadController::class, 'index']);
    //buscar
    Route::get('/documentos/buscar', [DocumentoController::class, 'buscar']);
    //Prompt bussines
    Route::post('/user/business-prompt', [ChatController::class, 'generarBusinessPrompt']);
    // Conversaciones (threads)
    Route::get('/conversaciones', [ConversacionController::class, 'index']);
    Route::get('/conversaciones/by-session/{session_id}', function ($session_id) {
        $conv = \App\Models\Conversacion::where('session_id', $session_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        return response()->json($conv);
    });
    Route::get('/conversaciones/{conversacion}', [ConversacionController::class, 'show']);
    Route::put('/conversaciones/{conversacion}', [ConversacionController::class, 'update']);
    Route::delete('/conversaciones/{conversacion}', [ConversacionController::class, 'destroy']);
    //reportes
    Route::get('/report/proyectos', [ReporteChatController::class, 'proyectos']);
    Route::post('/report/generar',  [ReporteChatController::class, 'generar']);
    //empresa
    Route::get('/empresa',  [EmpresaController::class, 'show']);
    Route::post('/empresa', [EmpresaController::class, 'update']);
    // Stripe
    Route::get('/checkout/{plan}', [App\Http\Controllers\StripeController::class, 'checkout'])->name('checkout')->middleware('auth');
    Route::get('/checkout/success', [App\Http\Controllers\StripeController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel', [App\Http\Controllers\StripeController::class, 'cancel'])->name('checkout.cancel');
    // Notifiche
    Route::get('/notifiche', [NotificaController::class, 'index']);
    Route::post('/notifiche', [NotificaController::class, 'store']);
    Route::delete('/notifiche/{notifica}', [NotificaController::class, 'destroy']);
    // URL Favoritas
    Route::get('/url-favoritas', [UrlFavoritaController::class, 'index']);
    Route::post('/url-favoritas', [UrlFavoritaController::class, 'store']);
    Route::delete('/url-favoritas/{urlFavorita}', [UrlFavoritaController::class, 'destroy']);
    //soporte
    Route::post('/support/chat', [SupportChatController::class, 'send']);
    // Teams
    Route::get('/teams', [TeamController::class, 'index']);
    Route::post('/teams', [TeamController::class, 'store']);
    Route::put('/teams/{team}', [TeamController::class, 'update']);
    Route::delete('/teams/{team}', [TeamController::class, 'destroy']);
    Route::post('/teams/{team}/invite', [TeamController::class, 'invite']);
    Route::post('/teams/{team}/members/{member}/role', [TeamController::class, 'updateRole']);
    Route::delete('/teams/{team}/members/{member}', [TeamController::class, 'removeMember']);
    Route::post('/teams/{team}/projects', [TeamController::class, 'assignProject']);
    Route::post('/teams/{team}/switch', [TeamController::class, 'switchTeam']);
    Route::get('/teams/invitations/{invitation}/accept', [TeamController::class, 'acceptInvitation'])->name('team.invitation.accept');
});

    Route::post('/teams/switch/personal', function() {
        Auth::user()->update(['current_team_id' => null]);
        return response()->json(['success' => true]);
    });
