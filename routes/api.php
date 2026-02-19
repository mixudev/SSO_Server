<?php

use App\Http\Controllers\Api\LogoutController;
use App\Http\Controllers\Api\UserInfoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('/user', [UserInfoController::class, 'show']);
    
    // Logout endpoints
    Route::post('/logout', [LogoutController::class, 'logout']); // Hanya logout session, token tetap valid
    Route::post('/revoke-token', [LogoutController::class, 'revokeToken']); // Revoke token saja, session tetap aktif
    Route::post('/logout-all', [LogoutController::class, 'logoutAll']); // Revoke semua token + logout semua session
});


