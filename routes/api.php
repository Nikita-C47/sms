<?php

use Illuminate\Http\Request;

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

// Маршруты, доступные через API
Route::middleware('auth:api')->group(function () {

    // Рейсы
    Route::get('/flights', 'Api\FlightsController@index');
    Route::get('/flights/{id?}', 'Api\FlightsController@view');
    Route::post('/flights', 'Api\FlightsController@create');
    Route::post('/flights/load', 'Api\FlightsController@load')->name('load-flights');
    Route::put('/flights/{id}', 'Api\FlightsController@edit');
    Route::delete('/flights/{id}', 'Api\FlightsController@delete');
});
