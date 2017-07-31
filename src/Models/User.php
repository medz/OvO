<?php

namespace Medz\Fans\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = ['email', 'phone', 'password', 'pw_password', 'pw_salt'];

    /**
     * Get auth password.
     *
     * @return string
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function getAuthPassword()
    {
        $password = request('password');
        if ($this->pw_salt && $this->pw_password && md5(md5($password).$this->pw_salt) === $this->pw_password) {
            return bcrypt($password);
        }

        return parent::getAuthPassword();
    }
}
