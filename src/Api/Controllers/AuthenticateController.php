<?php

namespace Medz\Fans\Api\Controllers;

use Tymon\JWTAuth\JWTAuth;
use Illuminate\Http\Request;

class AuthenticateController extends Controller
{
    public function authenticate(Request $request, JWTAuth $auth)
    {
        $credentials = $request->only(['email', 'password']);

        dd(
            $auth->attempt($credentials)
        );
    }
}
