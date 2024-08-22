<?php

use App\Http\Controllers\Api\MaklerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('maklers', MaklerController::class)->only('show');
