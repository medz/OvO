<?php

namespace App\Models;

use App\Services\UserAbility;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, SoftDeletes;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = ['email', 'phone', 'password', 'pw_password', 'pw_salt', 'deleted_at', 'remember_token'];

    /**
     * The iser roles.
     *
     * @param string $role
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function roles(string $role)
    {
        if (! $role) {
            return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
        }

        return $this->ability()->roles($role);
    }

    /**
     * The user ability.
     *
     * @param string $role
     * @param string $ability
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function ability($role = '', $ability = '')
    {
        $userAblity = app(UserAbility::class)->setUser($this);

        if ($ability) {
            if ($role = $userAblity->roels($role)) {
                return $role->ability($ability);
            }

            return false;
        } elseif ($role) {
            $ability = $role;

            return $userAblity->all($ability);
        }

        return $userAblity;
    }

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

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return int
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
