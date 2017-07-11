<?php

namespace Medz\Fans\Api\Controllers;

use Tymon\JWTAuth\JWTAuth;
use Illuminate\Http\Request;

class AuthenticateController extends Controller
{
    /**
     * Get user auth token & data.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Tymon\JWTAuth\JWTAuth $auth
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function authenticate(Request $request, JWTAuth $auth)
    {
        $credentials = $request->only(['email', 'password']);
        $token = $auth->attempt($credentials);

        return $token = $auth->attempt($credentials) !== false
            ? $this->response()->array([
                'token' => $token,
                'user' => $request->user(),
            ])->setStatusCode(201)
            : $this->response()->errorInternal('No token created.');
    }
}
