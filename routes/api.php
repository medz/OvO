<?php

use Medz\Fans\Api\Controllers;

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

$api->version('v1', function ($api) {

    /*
    |-----------------------------------------------------------------------
    | Not auth routes.
    |-----------------------------------------------------------------------
    |
    | Define routes that do not require authentication in the following
    | groups.
    |
    */

    $api->post('/authenticate', Controllers\AuthenticateController::class.'@authenticate');

    /*
    |-----------------------------------------------------------------------
    | Defined auth routes.
    |-----------------------------------------------------------------------
    |
    | Define the routes that need to be authenticated in the following
    | groups.
    |
     */

    $api->group(['middleware' => 'api.auth'], function ($api) {
        $api->get('/user', Controllers\AuthenticateController::class.'@getUser');
    });
});
