<?php

use App\Http\Controllers\MlWebhookController;
use Illuminate\Support\Facades\Route;

// Stage 5: Machine Learning Webhook Sync
Route::post('/ml/sync-evaluations', [MlWebhookController::class, 'sync'])->name('api.ml.sync');
