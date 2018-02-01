<?php

declare(strict_types=1);

namespace App\Api\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'refresh']]);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function guard(): Guard
    {
        return Auth::guard('api');
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     * @return \Illuminate\Http\JsonResponse
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function respondWithToken(string $token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60,
        ]);
    }

    /**
     * Get the login user name type.
     *
     * @param string $login
     * @return string
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function username(string $login): string
    {
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        } elseif (preg_match('/^(\+?0?86\-?)?((13\d|14[57]|15[^4,\D]|17[3678]|18\d)\d{8}|170[059]\d{7})$/', $login)) {
            return 'phone';
        }

        return 'login';
    }

    /**
     * Get a JWT token via given credentials.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function login(Request $request): JsonResponse
    {
        $login = (string) $request->input('login', '');
        $credentials = [
            $this->username($login) => $login,
            'password' => (string) $request->input('password', ''),
        ];

        if ($token = $this->guard()->attempt($credentials)) {
            return $this->respondWithToken($token);
        }

        return response()->json(['message' => '您输入的用户信息错误'], 422);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function logout(): JsonResponse
    {
        $this->guard()->logout();

        return response()->json(['message' => '退出成功']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function refresh(): JsonResponse
    {
        return $this->respondWithToken(
            $this->guard()->refresh()
        );
    }

    /**
     * Get authed me.
     *
     * @return \Illuminate\Http\JsonResponse
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function me(): JsonResponse
    {
        $user = $this->guard()->user();
        $user->setHidden(['password', 'deleted_at']);

        return response()->json(array_merge($user->toArray(), [
            'phone' => $user->phone,
            'email' => $user->email,
        ]), 200);
    }
}
