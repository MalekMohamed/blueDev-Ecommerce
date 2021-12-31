<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('/update-product/{id}', [ProductController::class, 'update'])->name('product.update');
Route::post('/upload-file', [ProductController::Class,'uploadFile']);
Route::resource('/products', ProductController::class)->except('update');
Route::resource('/categories', CategoryController::class);
Route::resource('/brands', BrandController::class);
Route::resource('/users', UserController::class)->except('store');
Route::resource('/cart', CartController::class);
Route::get('/user/send-verification-email', [AuthController::class, 'resendVerificationMail'])->name('email.verify.resend');
Route::post('/forget-password', [AuthController::class, 'SendResetPasswordMail'])->name('email.resetPassword.send');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('auth.resetPassword');
Route::post('/login', [AuthController::class, 'login'])->name('login.user');
Route::post('/register', [UserController::class, 'store'])->name('register');
Route::post('/update-profile', [AuthController::class, 'updateUserData'])->name('profile.update');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout.user');


