<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
	return response()->json(['success' => true]);
});

Route::group(['middleware' => ['api', 'guest', 'throttle:auth']], function () {
	Route::post('/auth/login', [\App\Http\Controllers\AuthController::class, 'login']);
	Route::post('/auth/register', [\App\Http\Controllers\AuthController::class, 'register']);
	Route::post('/auth/forgot-password', [\App\Http\Controllers\AuthController::class, 'forgotPassword']);
	Route::put('/auth/reset-password/{token}', [\App\Http\Controllers\AuthController::class, 'resetPassword'])->middleware('signed:relative')->name('password.update');
	Route::post('/auth/verify-email', [\App\Http\Controllers\AuthController::class, 'verifyEmail'])->middleware('signed:relative')->name('verification.verify');
	Route::post('/auth/resend-verification', [\App\Http\Controllers\AuthController::class, 'resendVerification'])->name('verification.send');
});

Route::group(['middleware' => ['api', 'auth:sanctum', 'throttle:api']], function () {
	Route::delete('/auth/logout', [\App\Http\Controllers\AuthController::class, 'logout']);
	Route::put('/auth/change-email', [\App\Http\Controllers\AuthController::class, 'changeEmail']);
	Route::put('/auth/change-password', [\App\Http\Controllers\AuthController::class, 'changePassword']);

	Route::post('/users/delete-data', [\App\Http\Controllers\UserController::class, 'deleteData']);

	Route::apiResources([
		'actions' => \App\Http\Controllers\ActionController::class,
		'action-types' => \App\Http\Controllers\ActionTypeController::class,
		'users' => \App\Http\Controllers\UserController::class,
	]);
});
