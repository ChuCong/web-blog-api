<?php

use App\Core\AppConst;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ArticleController;
use Illuminate\Http\Request;


use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('google', [AuthController::class, 'loginGoogle']);
});
// Route::middleware(['auth:sanctum'])->group(function () {
Route::middleware(['optional_auth'])->group(function () {});
// });

Route::middleware(['auth:sanctum'])->group(function () {

    // Thông tin user
    Route::get('/user/profile', [UserController::class, 'getProfile']);
    Route::put('/user/profile', [UserController::class, 'updateProfile']);
    Route::post('/user/avatar', [UserController::class, 'updateAvatar']);
    Route::get('/user/my-courses', [UserController::class, 'getMyCourses']);
    Route::post('/user/generate-certificate', [UserController::class, 'generateCertificate']);
});
Route::get('/category', [CategoryController::class, 'getList']);
Route::get('/category/{slug}', [CategoryController::class, 'getBySlug']);
Route::get('/articles', [ArticleController::class, 'getList']);
Route::get('/articles/{slug}', [ArticleController::class, 'getBySlug']);
Route::get('/articles/category/{slug}', [ArticleController::class, 'getByCategoryId']);
