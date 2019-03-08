<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\VerificationCode;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Login as LoginRequest;
use Illuminate\Auth\AuthenticationException;
use App\Http\Requests\SendPhoneNumberVerfiyCode as SendPhoneNumberVerfiyCodeRequest;

class AuthController extends Controller
{
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
    public function resolve(LoginRequest $request)
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
            'id' => Str::uuid()->toString(),
            'phone_verified_at' => Carbon::now(),
        ]));
    }
}
