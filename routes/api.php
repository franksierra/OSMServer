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

Route::prefix('v1')->namespace('v1')
//    ->middleware('api.authorization')
    ->group(function () {
    Route::prefix('street')->group(function () {
        Route::get('', 'Street@index')->name('api.v1.street.index');
    });
});


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
