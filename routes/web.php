<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Frontend\PostController as FrontendPostController;
use App\Http\Controllers\HomeController;
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
    Route::resource('posts', \App\Http\Controllers\Admin\PostController::class)->names('admin.posts');
});

Route::get('/posts/{slug}', [FrontendPostController::class, 'show'])->name('posts.show');
