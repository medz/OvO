<?php

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

// Auth
Route::post('/auth/jwt', 'AuthController@resolve');
Route::post('/auth/verify-code', 'AuthController@sendPhoneVerifyCode');

// Internation Telephone code
Route::get('/international-telephone-codes', 'InternationalTelephoneCodeController@index');
