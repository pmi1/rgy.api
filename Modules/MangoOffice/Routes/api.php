<?php

use Illuminate\Http\Request;
use Laravel\Passport\Passport;
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

Route::middleware('auth:api')->get('/', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['auth.cors', 'auth:api'], 'prefix' => 'mango-office'], function () {
    Route::get('stats', 'MangoOfficeController@getStats');
    Route::get('record', 'MangoOfficeController@getRecord');
});