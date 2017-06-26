<?php

namespace Medz\Wind\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    public function findForPassport(string $username)
    {
        return $this->find(1);
    }

    public function validateForPassportPasswordGrant()
    {
        return true;
    }
}
