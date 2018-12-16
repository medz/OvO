<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Overtrue\EasySms\PhoneNumber;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use App\Sms\Utils\TextVerificationCode;
use App\Http\Requests\Login as LoginRequest;
use Illuminate\Auth\AuthenticationException;
use App\Http\Requests\SendPhoneNumberVerfiyCode as SendPhoneNumberVerfiyCodeRequest;

class AuthController extends Controller
{
    public function sendPhoneVerifyCode(SendPhoneNumberVerfiyCodeRequest $request): Response
    {
        TextVerificationCode::send(
            $request->input('international_telephone_code'),
            $request->input('phone')
        );

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * Login or register a user.
     */
    public function resolve(LoginRequest $request)
    {
        // Find or register a user.
        $user = User::where('phone', $request->input('phone'))->where('international_telephone_code', $request->input('international_telephone_code'))->firstOr(function () use ($request) {
            return $this->register($request);
        });

        // If verify type is "password"
        if ($request->input('verify_type') === 'password') {
            $this->loginWithPassword($request, $user);
        }

        // Remove verify code.
        TextVerificationCode::remove($user->international_telephone_code, $user->phone);

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
