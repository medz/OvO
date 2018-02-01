<?php

use App\Api\Controllers;
use App\Api\Middleware\CrossDomain;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Routing\Registrar as RouteContract;

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

Route::group(['prefix' => 'v1', 'middleware' => CrossDomain::class], function (RouteContract $api) {
    $api->group(['prefix' => 'auth'], function (RouteContract $api) {
        $api->post('login', Controllers\AuthController::class.'@login');
        $api->any('logout', Controllers\AuthController::class.'@logout');
        $api->any('refresh', Controllers\AuthController::class.'@refresh');
        $api->get('me', Controllers\AuthController::class.'@me');
    });
    $api->get('user', Controllers\AuthController::class.'@me');
    $api->post('register', Controllers\UserRegisterController::class);
    $api->group(['prefix' => 'users'], function (RouteContract $api) {
        $api->get('', Controllers\User\UserController::class.'@index');
        $api->get('{user}', Controllers\User\UserController::class.'@show');
    });
    $api->group(['prefix' => 'tags'], function (RouteContract $api) {
        $api->get('', Controllers\TagController::class.'@index');
        $api->get('{tag}', Controllers\TagController::class.'@show');
    });

    $api->get('forums', Controllers\Forum\ForumController::class.'@index');
    $api->get('forums/{forum}', Controllers\Forum\ForumController::class.'@show');
    $api->get('forums/{forum}/categories', Controllers\Forum\CategoryController::class.'@index');
    $api->get('forum->categories/{category}', Controllers\Forum\CategoryController::class.'@show');
    $api->get('forums/{forum}/topics', Controllers\Forum\TopicController::class.'@index');
    $api->post('forums/{forum}/topics', Controllers\Forum\TopicController::class.'@store');
    $api->get('forum->topics', Controllers\Forum\TopicController::class.'@all');
    $api->get('forum->topics/{topic}', Controllers\Forum\TopicController::class.'@show');
});
