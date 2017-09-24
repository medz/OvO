<?php

namespace App\Services\Auth;

use Tymon\JWTAuth\JWTAuth;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;

class JwtGuard extends Guard
{
    use GuardHelpers;

    /**
     * The JWT auth.
     *
     * @var \Tymon\JWTAuth\JWTAuth
     */
    protected $auth;

    /**
     * Create a new authentication guard.
     *
     * @param \Tymon\JWTAuth\JWTAuth $auth
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function __construct(JWTAuth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function user()
    {
        if (! $this->user) {
            return $this->user;
        }

        $user = null;
        if (! ($token = $this->auth->getToken())) {
            $user = $this->auth->toUser($token) ?: null;
        }

        return $this->user = $user;
    }

    /**
     * Validate a user's credentials.
     *
     * @param array $credentials
     * @return bool
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function validate(array $credentials = [])
    {
        return (bool) $this->auth->attempt($credentials);
    }
}
