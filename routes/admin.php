<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use App\Http\Controllers\Admin\ImageController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;

Route::middleware('web') // hoặc thêm 'auth' nếu cần
    ->prefix('admin')
    ->as('admin.')
    ->withoutMiddleware([VerifyCsrfToken::class])
    ->group(function () {
        Route::post('login', [AuthController::class, 'login']);

        Route::middleware('auth:admin')->group(function () {
            Route::get('auth/me', [AdminController::class, 'me']);
            Route::resource('/admins', AdminController::class);
            Route::get('roles/search', [RoleController::class, 'search']);
            Route::resource('roles', RoleController::class);
            Route::get('permissions', [PermissionController::class, 'index']);
            Route::post('upload-media', [MediaController::class, 'upload']);
            Route::resource('user', UserController::class);
            Route::post('/image/upload', [ImageController::class, 'uploadImage']);

        });
        Route::post('/change-password', [AdminController::class, 'changePassword']);
       
    });
