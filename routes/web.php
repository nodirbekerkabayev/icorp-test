<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiTestController;

Route::get('/', [ApiTestController::class, 'index'])->name('api-test.index');

Route::post('/api-test/start', [ApiTestController::class, 'startProcess'])->name('api-test.start');

Route::post('/api-test/webhook', [ApiTestController::class, 'webhook'])->name('api-test.webhook');

Route::get('/api-test/status', [ApiTestController::class, 'checkStatus'])->name('api-test.status');

Route::get('/api-test/final', [ApiTestController::class, 'getFinalMessage'])->name('api-test.final');
