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

        return ($token = $auth->attempt($credentials)) !== false
            ? $this->response()->array([
                'token' => $token,
                'user' => array_merge($request->user()->toArray(), [
                    'email' => $request->user()->email,
                    'phone' => $request->user()->phone,
                ]),
            ])->setStatusCode(201)
            : $this->response()->error('账号或者密码错误.', 422);
    }

    /**
     * Get current user.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function getUser(Request $request)
    {
        $user = $request->user();
        return $this->response()
            ->array(array_merge($user->toArray(), [
                'email' => $user->email,
                'phone' => $user->phone,
            ]))
            ->setStatusCode(200);
    }
}
