<?php

use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [LoginController::class, 'submit']);
Route::post('/login/verify', [LoginController::class, 'verify']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/pharmacy', [PharmacyController::class, 'show']);
    Route::post('/pharmacy', [PharmacyController::class, 'update']);

    Route::post('/order', [OrderController::class, 'store']);
    Route::post('/order/{order}/accept', [OrderController::class, 'accept']);
    Route::post('/order/{order}/complete', [OrderController::class, 'complete']);

    Route::get('/user', function(Request $request) {
        return $request->user();
    });
    Route::post('/user', function(Request $request) {
        $request->validate([
            'name' => 'required'
        ]);

        $user = $request->user();

        $user->update($request->only('name'));

        return response()->json(['username' => $request->input('name')], 200);
    });

    Route::get('/logout', function(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'logged out'], 200);
    });
});
