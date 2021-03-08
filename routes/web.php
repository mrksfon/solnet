<?php

use App\Http\Controllers\FavoritesController;
use App\Http\Controllers\RepliesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ThreadsController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/threads',[ThreadsController::class,'index']);
Route::get('/threads/create',[ThreadsController::class,'create']);
Route::get('/threads/{channel}/{thread}',[ThreadsController::class,'show']);
Route::post('/threads',[ThreadsController::class,'store']);
Route::get('/threads/{channel}',[ThreadsController::class,'index']);

Route::post('/threads/{channel}/{thread}/replies',[RepliesController::class,'store']);

Route::post('/replies/{reply}/favorites',[FavoritesController::class,'store']);
