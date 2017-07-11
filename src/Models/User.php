<?php

namespace Medz\Fans\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    public function getAuthPassword()
    {
        // dd(request('password'));
        // dd($this);
        return parent::getAuthPassword();
    }
}
