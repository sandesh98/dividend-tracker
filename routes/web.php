<?php

use App\Http\Controllers\DividendController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PortfolioController::class, 'index'])->name('portfolio.index');
Route::get('/show/{stock}', [PortfolioController::class, 'show'])->name('portfolio.show');

Route::get('/dividend', [DividendController::class, 'index'])->name('dividend.index');

Route::get('/transactions', [TransactionController::class, 'index'])->name('transaction.index');
