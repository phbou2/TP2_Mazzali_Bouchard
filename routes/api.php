<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Routes du TP2 ici : 
Route::middleware(['throttle:5,1'])->group( function(){
    Route::post('/signin', 'App\Http\Controllers\AuthController@login');
    Route::post('/signup', 'App\Http\Controllers\AuthController@register');
    Route::middleware('auth:sanctum')->group(function(){
        Route::post('/signout', 'App\Http\Controllers\AuthController@logout');
    });
});

Route::middleware(['throttle:60,1'])->group(function(){
    Route::middleware('auth:sanctum')->group(function(){
        Route::post('/createfilm', 'App\Http\Controllers\FilmController@create');
        Route::post('/updatefilm/{id}', 'App\Http\Controllers\FilmController@update');
    });
});

Route::get('/films', 'App\Http\Controllers\FilmController@index');




