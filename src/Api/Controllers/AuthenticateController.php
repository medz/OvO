<?php

namespace Medz\Fans\Api\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;

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
