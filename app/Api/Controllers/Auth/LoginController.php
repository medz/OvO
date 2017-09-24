<?php

namespace App\Api\Controllers\Auth;

use Tymon\JWTAuth\JWTAuth;
use Illuminate\Http\Request;
use App\Api\Controllers\Controller;

class LoginController extends Controller
{
    /**
     * Auth manager.
     *
     * @var \Tymon\JWTAuth\JWTAuth
     */
    protected $auth;

    /**
     * Create the controller instance.
     *
     * @param \Tymon\JWTAuth\JWTAuth $auth
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function __construct(JWTAuth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle a login request to the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function login(Request $request)
    {
        $login = strval($request->input('login'));
        $credentials = [
            $this->username($login) => $login,
            'password' => $request->input('password'),
        ];

        if (! ($token = $this->auth->attempt($credentials))) {
            return response()->json(['message' => trans('auth.failed')], 422);
        }

        return response()->json([
            'token' => $token,
            'ttl' => config('jwt.ttl'),
            'refresh_ttl' => config('jwt.refresh_ttl'),
        ])->setStatusCode(201);
    }

    /**
     * Get user login field.
     *
     * @param string $login
     * @param string $default
     * @return string
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function username(string $login, string $default = 'login'): string
    {
        $map = [
            'email' => filter_var($login, FILTER_VALIDATE_EMAIL),
            'phone' => preg_match('/^(\+?0?86\-?)?((13\d|14[57]|15[^4,\D]|17[3678]|18\d)\d{8}|170[059]\d{7})$/', $login),
        ];

        foreach ($map as $field => $value) {
            if ($value) {
                return $field;
            }
        }

        return $default;
    }
}
