<?php

use App\Core\AppConst;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\LessonController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ResourceController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\TestimonialController;
use App\Http\Controllers\Api\UserNotificationController;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ContactMsdController;


use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('google', [AuthController::class, 'loginGoogle']);
});
// Route::middleware(['auth:sanctum'])->group(function () {
Route::middleware(['optional_auth'])->group(function () {

});
// });

Route::middleware(['auth:sanctum'])->group(function () {

    // Thông tin user
    Route::get('/user/profile', [UserController::class, 'getProfile']);
    Route::put('/user/profile', [UserController::class, 'updateProfile']);
    Route::post('/user/avatar', [UserController::class, 'updateAvatar']);
    Route::get('/user/my-courses', [UserController::class, 'getMyCourses']);
    Route::post('/user/generate-certificate', [UserController::class, 'generateCertificate']);
});