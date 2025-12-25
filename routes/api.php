<?php

use App\Http\Controllers\Api\DividendController;
use Illuminate\Support\Facades\Route;

Route::apiResource('dividends', DividendController::class)->only('index');
