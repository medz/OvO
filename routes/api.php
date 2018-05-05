<?php

use App\Api\Controllers as API;
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
        $api->post('login', API\AuthController::class.'@login');
        $api->any('logout', API\AuthController::class.'@logout');
        $api->any('refresh', API\AuthController::class.'@refresh');
        $api->get('me', API\AuthController::class.'@me');
    });
    $api->get('user', API\AuthController::class.'@me');
    $api->post('register', API\UserRegisterController::class);
    $api->group(['prefix' => 'users'], function (RouteContract $api) {
        $api->get('', API\User\UserController::class.'@index');
        $api->get('{user}', API\User\UserController::class.'@show');
    });
    $api->group(['prefix' => 'tags'], function (RouteContract $api) {
        $api->get('', API\TagController::class.'@index');
        $api->get('{tag}', API\TagController::class.'@show');
    });

    $api->get('forums', API\Forum\ForumController::class.'@index');
    $api->get('forums/{forum}', API\Forum\ForumController::class.'@show');
    $api->get('forums/{forum}/categories', API\Forum\CategoryController::class.'@index');
    $api->get('forum->categories/{category}', API\Forum\CategoryController::class.'@show');
    $api->get('forums/{forum}/topics', API\Forum\TopicController::class.'@index');
    $api->post('forums/{forum}/topics', API\Forum\TopicController::class.'@store');
    $api->get('forum->topics', API\Forum\TopicController::class.'@all');
    $api->get('forum->topics/{topic}', API\Forum\TopicController::class.'@show');
});
