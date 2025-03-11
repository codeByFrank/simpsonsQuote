<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuoteController;
use Illuminate\Support\Facades\File;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware(['auth:api'])->group(function () {
  Route::get('/quotes', [QuoteController::class, 'index']); 
});

Route::get('/{any}', function () {
  return File::get(public_path('index.html'));
})->where('any', '^(?!api).*$');