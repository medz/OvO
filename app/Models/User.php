<?php

declare(strict_types=1);

namespace App\Models;

use Laravel\Scout\Searchable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'phone',
        'international_telephone_code', 'phone_verified_at',
        'email', 'email_verified_at', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'id' => $this->getOriginal('id'),
            'name' => $this->getOriginal('name'),
            'international_telephone_code' => $this->getOriginal('international_telephone_code'),
            'phone' => $this->getOriginal('phone'),
            'email' => $this->getOriginal('email'),
        ];
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * The user extras.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function extras(): HasMany
    {
        return $this->hasMany(UserExtra::class);
    }

    /**
     * The user jurisdiction nodes.
     */
    public function jurisdictions(): HasMany
    {
        return $this->hasMany(Jurisdiction::class);
    }
}
