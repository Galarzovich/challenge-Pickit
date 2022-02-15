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

Route::post('auth/login', 'AuthController@login');
Route::post('auth/signup', 'AuthController@signup');

Route::group(['middleware' => ['auth:api']], function(){

    Route::apiResource('personas','PersonaController');
    Route::apiResource('servicios','ServicioController');
    Route::apiResource('vehiculos','VehiculoController');
    Route::apiResource('ventas','VentaController');

    Route::get('vehiculos/{id}/servicios','VehiculoController@getServicios');
    Route::group(['prefix' => 'users' ], function(){
        Route::get("me","UsersController@currentUser");
    });
});
