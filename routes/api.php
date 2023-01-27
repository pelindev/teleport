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

Route::group(['prefix' => 'users'], function () {
    Route::post(
        'create',
        'App\Http\Controllers\UserController@create'
    );
    Route::get(
        '',
        'App\Http\Controllers\UserController@getAll'
    );
    Route::get(
        '{id}',
        'App\Http\Controllers\UserController@getOneById'
    );
    Route::patch(
        '{id}',
        'App\Http\Controllers\UserController@update'
    );
});

Route::group(['prefix' => 'payments'], function () {
    Route::post(
        'create',
        'App\Http\Controllers\PaymentController@create'
    );

    Route::get(
        'cancel/{id}',
        'App\Http\Controllers\PaymentController@cancelTransaction'
    );

    Route::get(
        '',
        'App\Http\Controllers\PaymentController@getAll'
    );

    Route::get(
        '{id}',
        'App\Http\Controllers\PaymentController@getById'
    );
});
