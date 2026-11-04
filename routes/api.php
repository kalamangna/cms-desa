<?php

use App\Http\Controllers\Api\TelemetryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Rute internal API untuk integrasi sistem dan dasbor monitoring terpusat.
|
*/

Route::middleware('monitoring.auth')->prefix('v1/internal')->group(function () {
    Route::get('/telemetry', [TelemetryController::class, 'index']);
});
