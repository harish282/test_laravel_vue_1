<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\OrderController;

Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($request->only('email', 'password'))) {
        $user = Auth::user();
        $token = $user->createToken('api')->plainTextToken;
        return response()->json(['token' => $token]);
    }

    return response()->json(['message' => 'Invalid credentials'], 401);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/my-orders', [OrderController::class, 'myOrders']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);
});
