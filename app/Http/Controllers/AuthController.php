<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\VerificationCode;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Login as LoginRequest;
use App\Http\Requests\SendPhoneNumberVerfiyCode as SendPhoneNumberVerfiyCodeRequest;

class AuthController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['login', 'refresh']);
    }

    /**
     * Get the guard.
     */
    protected function guard(): Guard
    {
        return Auth::guard();
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
    protected function create(array $payload): User
    {
        return User::create(array_merge($payload, [
            'phone_verified_at' => Carbon::now(),
        ]));
    }

    /**
     * Send a phone verification code.
     * @param \App\Http\Requests\SendPhoneNumberVerfiyCode @request
     * @return \Illuminate\Http\Response
     */
    public function sendVerificationCode(SendPhoneNumberVerfiyCodeRequest $request): Response
    {
        VerificationCode::send(
            $request->input('international_telephone_code'),
            $request->input('phone')
        );

        return $this->withHttpNoContent();
    }

    /**
     * Login or register a user.
     */
    public function login(LoginRequest $request)
    {
        // Find or register a user.
        $user = User::where('phone', $request->input('phone'))->where('international_telephone_code', $request->input('international_telephone_code'))->firstOr(function () use ($request) {
            return $this->create($request->only([
                'phone', 'international_telephone_code',
            ]));
        });

        // Remove verification code.
        VerificationCode::instance($user->international_telephone_code, $user->phone)->remove();

        return $this->respondWithToken(
            $this->guard()->login($user)
        );
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
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\Response
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function logout(): Response
    {
        $this->guard()->logout();

        return $this->withHttpNoContent();
    }
}
