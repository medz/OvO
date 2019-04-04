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

// Send Phone verification code.
Route::post('/verification-code/phone', 'AuthController@sendVerificationCode');

// Auth
Route::post('/auth/login', 'AuthController@login');
Route::post('/auth/logout', 'AuthController@logout');
Route::post('/auth/refresh', 'AuthController@refresh');
Route::get('/auth/me', 'UserController@show')->middleware('auth');

// Internation Telephone code
Route::get('/international-telephone-codes', 'InternationalTelephoneCodeController@index');
Route::post('/international-telephone-codes', 'InternationalTelephoneCodeController@store');
Route::patch('/international-telephone-codes/{ttc}', 'InternationalTelephoneCodeController@update');
Route::delete('/international-telephone-codes/{id}', 'InternationalTelephoneCodeController@destroy');

// Jurisdiction
Route::get('/jurisdictions', 'JurisdictionController@nodes');
Route::put('/users/{user}/jurisdictions', 'JurisdictionController@sync');
Route::put('/users/{user}/jurisdictions/{node}', 'JurisdictionController@attach');
Route::delete('/users/{user}/jurisdictions/{node}', 'JurisdictionController@detach');

// Storage Uploaded File
Route::post('/upload', 'StorageController');

// User
Route::get('/users', 'UserController@index');
Route::get('/users/{user}', 'UserController@show');

// Talk
Route::apiResource('/talks', 'TalkController', [
    'except' => ['update'],
]);

// Forum nodes
Route::apiResource('/forum/nodes', 'ForumNodeController');

// Forum threads
Route::apiResource('/forum/threads', 'ForumThreadController', [
    'except' => ['store'],
]);
Route::post('/forum/nodes/{node}/threads', 'ForumThreadController@store');
Route::put('/forum/nodes/{node}/threads/{thread}', 'ForumThreadController@transform');

// Comments
Route::apiResource('/comments', 'CommentController', [
    'only' => ['index', 'store', 'destroy'],
]);
