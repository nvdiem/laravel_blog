<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Frontend\PostController as FrontendPostController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [FrontendPostController::class, 'index']);
Route::get('/posts', [FrontendPostController::class, 'index'])->name('posts.index');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('analytics', [\App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('admin.analytics.index');
    Route::resource('posts', \App\Http\Controllers\Admin\PostController::class)->names('admin.posts');
    Route::post('posts/{post}/autosave', [\App\Http\Controllers\Admin\PostController::class, 'autosave'])->name('admin.posts.autosave');
    Route::post('posts/bulk', [\App\Http\Controllers\Admin\PostController::class, 'bulkAction'])->name('admin.posts.bulk');
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)->names('admin.categories');
    Route::get('tags/suggest', [\App\Http\Controllers\Admin\PostController::class, 'suggestTags'])->name('admin.tags.suggest');
    Route::get('media', [\App\Http\Controllers\Admin\MediaController::class, 'index'])->name('admin.media.index');
    Route::post('media/upload', [\App\Http\Controllers\Admin\MediaController::class, 'upload'])->name('admin.media.upload');
    Route::patch('media/{media}', [\App\Http\Controllers\Admin\MediaController::class, 'update'])->name('admin.media.update');
    Route::delete('media/{media}', [\App\Http\Controllers\Admin\MediaController::class, 'destroy'])->name('admin.media.destroy');
    Route::get('users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users.index');
    Route::post('users/{user}/role', [\App\Http\Controllers\Admin\UserController::class, 'updateRole'])->name('admin.users.update-role');
    Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class)->names('admin.roles');
    Route::get('site-settings', [\App\Http\Controllers\Admin\SiteSettingsController::class, 'index'])->name('admin.site-settings.index');
    Route::post('site-settings', [\App\Http\Controllers\Admin\SiteSettingsController::class, 'update'])->name('admin.site-settings.update');
});

Route::get('/posts/{slug}', [FrontendPostController::class, 'show'])->name('posts.show');
Route::get('/category/{slug}', [\App\Http\Controllers\Frontend\CategoryController::class, 'show'])->name('categories.show');
Route::get('/preview/posts/{post}', [\App\Http\Controllers\Frontend\PostController::class, 'preview'])->name('posts.preview')->middleware('signed');
