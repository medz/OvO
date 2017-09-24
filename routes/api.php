<?php

use App\Api\Controllers;
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

Route::group(['prefix' => 'v1'], function (RouteContract $api) {

    /*
    |-----------------------------------------------------------------------
    | Not auth routes.
    |-----------------------------------------------------------------------
    |
    | Define routes that do not require authentication in the following
    | groups.
    |
    */

    $api->post('/login', Controllers\Auth\LoginController::class.'@login');
    $api->post('/register', Controllers\Auth\RegisterController::class.'@store');

    /*
    |-----------------------------------------------------------------------
    | Defined not auth users routes.
    |-----------------------------------------------------------------------
    |
    | Define the public API associated with the users.
    |
    */

    $api->group(['prefix' => 'users'], function (RouteContract $api) {
        $api->get('/', Controllers\User\UserController::class.'@index');
        $api->get('/{user}', Controllers\User\UserController::class.'@show');
    });

    // $api->apiResource('/users', Controllers\User\UserController::class);

    /*
    |-----------------------------------------------------------------------
    | Defined not auth forum routes.
    |-----------------------------------------------------------------------
    |
    | Define the public API associated with the forum.
    |
    */

    // $api->group(['prefix' => '/forum'], function ($api) {
    //     $api->resource('/categories', Controllers\ForumCategoryController::class);
    // });

    /*
    |-----------------------------------------------------------------------
    | Defined auth routes.
    |-----------------------------------------------------------------------
    |
    | Define the routes that need to be authenticated in the following
    | groups.
    |
    */

    $api->group(['middleware' => 'auth:api'], function (RouteContract $api) {

        // Authenticated User.
        $api->group(['prefix' => 'user'], function (RouteContract $api) {
            $api->get('/', Controllers\User\AuthenticatedController::class.'@show');
        });
    });
});
