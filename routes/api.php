<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\TipologiaController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ActividadController;
use App\Http\Controllers\ConversacionController;
use App\Http\Controllers\ReporteChatController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\NotificaController;
use App\Http\Controllers\UrlFavoritaController;
use App\Http\Controllers\SupportChatController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\ClaudeController;

// ─── Auth (público) ────────────────────────────
Route::post('/login', [AuthController::class, 'login']);

// ─── Protegidas con Sanctum ────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Tokens / Plan
    Route::get('/user/tokens', function () {
        $service = new \App\Services\TokenService();
        return response()->json($service->getHeaderData(\Illuminate\Support\Facades\Auth::user()));
    });

    // Proyectos
    Route::get('/proyectos', [ProyectoController::class, 'index']);
    Route::post('/proyectos', [ProyectoController::class, 'store']);
    Route::put('/proyectos/{proyecto}', [ProyectoController::class, 'update']);
    Route::delete('/proyectos/{proyecto}', [ProyectoController::class, 'destroy']);

    // Categorías
    Route::get('/proyectos/{proyecto}/categorias', [CategoriaController::class, 'index']);
    Route::post('/categorias', [CategoriaController::class, 'store']);
    Route::put('/categorias/{categoria}', [CategoriaController::class, 'update']);
    Route::delete('/categorias/{categoria}', [CategoriaController::class, 'destroy']);

    // Tipologías
    Route::get('/categorias/{categoria}/tipologias', [TipologiaController::class, 'index']);
    Route::post('/tipologias', [TipologiaController::class, 'store']);
    Route::put('/tipologias/{tipologia}', [TipologiaController::class, 'update']);
    Route::delete('/tipologias/{tipologia}', [TipologiaController::class, 'destroy']);

    // Documentos
    Route::post('/documentos', [DocumentoController::class, 'store']);
    Route::post('/documentos/check-size', [DocumentoController::class, 'checkSize']);
    Route::post('/documentos/download-multiple', [DocumentoController::class, 'downloadMultiple']);
    Route::post('/chat/con-documenti', [DocumentoController::class, 'chatConDocumenti']);
    Route::get('/proyectos/{proyecto}/dataroom', [DocumentoController::class, 'porProyecto']);
    Route::delete('/documentos/{documento}', [DocumentoController::class, 'destroy']);
    Route::get('/documentos/{documento}/download', [DocumentoController::class, 'download']);
    Route::get('/documentos/{documento}/info', [DocumentoController::class, 'info']);
    Route::put('/documentos/{documento}', [DocumentoController::class, 'update']);
    Route::get('/documentos/{documento}/preview-token', [DocumentoController::class, 'previewToken']);
    Route::get('/documentos/{documento}/preview', [DocumentoController::class, 'preview']);
    Route::get('/documentos/buscar', [DocumentoController::class, 'buscar']);
    
    Route::get('/documentos/buscar-chat', [DocumentoController::class, 'buscarParaChat']);

    // AI
    Route::post('/claude/analizar', [ClaudeController::class, 'analizarYGuardar']);
    Route::post('/chat/send', [ChatController::class, 'send']);
    Route::post('/chat/v2/send', [ChatController::class, 'sendV2']);
    Route::post('/user/business-prompt', [ChatController::class, 'generarBusinessPrompt']);

    // Conversaciones
    Route::get('/conversaciones/by-session/{session_id}', function ($session_id) {
        $conv = \App\Models\Conversacion::where('session_id', $session_id)
            ->where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->firstOrFail();
        return response()->json($conv);
    });
    Route::get('/conversaciones', [ConversacionController::class, 'index']);
    Route::get('/conversaciones/{conversacion}', [ConversacionController::class, 'show']);
    Route::put('/conversaciones/{conversacion}', [ConversacionController::class, 'update']);
    Route::delete('/conversaciones/{conversacion}', [ConversacionController::class, 'destroy']);

    // Actividades
    Route::get('/actividades', [ActividadController::class, 'index']);

    // Reportes
    Route::get('/report/proyectos', [ReporteChatController::class, 'proyectos']);
    Route::post('/report/generar', [ReporteChatController::class, 'generar']);

    // Empresa
    Route::get('/empresa', [EmpresaController::class, 'show']);
    Route::post('/empresa', [EmpresaController::class, 'update']);

    // Notifiche
    Route::get('/notifiche', [NotificaController::class, 'index']);
    Route::post('/notifiche', [NotificaController::class, 'store']);
    Route::delete('/notifiche/{notifica}', [NotificaController::class, 'destroy']);

    // URL Favoritas
    Route::get('/url-favoritas', [UrlFavoritaController::class, 'index']);
    Route::post('/url-favoritas', [UrlFavoritaController::class, 'store']);
    Route::delete('/url-favoritas/{urlFavorita}', [UrlFavoritaController::class, 'destroy']);

    // Soporte
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
});