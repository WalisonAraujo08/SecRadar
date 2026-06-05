<?php

use App\Http\Controllers\Api\AgentController;
use Illuminate\Support\Facades\Route;

// Endpoints exclusivos para o agente desktop Windows
Route::prefix('agent')->middleware('agent.token')->group(function () {
    Route::get('/status', [AgentController::class, 'status']);
    Route::post('/scan', [AgentController::class, 'triggerScan']);
    Route::post('/seen', [AgentController::class, 'markAlertsSeenByAgent']);
});
