<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\OtpVerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;


Route::post('/register', [RegisterController::class, 'register']);
Route::post('/verify-otp', [OtpVerificationController::class, 'verifyOtp']);
Route::post('/login', [LoginController::class, 'login']);

Route::post('/password-reset/send-otp', [ForgotPasswordController::class, 'sendOtp']);
Route::post('/password-reset/reset', [ForgotPasswordController::class, 'resetPassword']);
