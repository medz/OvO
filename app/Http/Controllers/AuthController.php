<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Login as LoginRequest;
use Illuminate\Auth\AuthenticationException;

class AuthController extends Controller
{
    /**
     * Login or register a user.
     */
    public function resolve(LoginRequest $request)
    {
        // Find or register a user.
        $user = User::firstOr($request->only([
            'phone', 'international_telephone_code',
        ]), function () use ($request) {
            return $this->register($request);
        });

        // If verify type is "password"
        if ($request->input('verify_type') === 'password') {
            $this->loginWithPassword($request, $user);
        }

        return $this->respondWithToken(
            $this->guard()->login($user)
        );
    }

    /**
     * Get the guard.
     */
    protected function guard(): Guard
    {
        return Auth::guard();
    }

    /**
     * Login a user with password.
     */
    protected function loginWithPassword(LoginRequest $request, $user)
    {
        $credentials = $request->only(['password']);
        if (! $this->guard()->getProvider()->validateCredentials($credentials)) {
            throw new AuthenticationException;
        }
    }

    /**
     * Make a json response with token.
     */
    protected function respondWithToken(string $token): JsonResponse
    {
        return new JsonResponse([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60,
        ], JsonResponse::HTTP_OK);
    }

    /**
     * register a user.
     */
    protected function register(LoginRequest $request): User
    {
        if ($request->input('verify_type') === 'password') {
            throw new AuthenticationException;
        }

        $user = new User($request->only([
            'phone', 'international_telephone_code',
        ]));
        $user->name = Str::uuid();
        $user->phone_verified_at = new Carbon;
        $user->save();

        return $user;
    }
}
