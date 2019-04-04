<?php

declare(strict_types=1);

namespace App\Models;

use App\ModelMorphMap;
use Laravel\Scout\Searchable;
use EloquentFilter\Filterable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use Searchable;
    use Filterable;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'international_telephone_code',
        'phone', 'phone_verified_at',
        'email', 'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'phone_verified_at' => 'timestamp',
        'email_verified_at' => 'timestamp',
    ];

    /**
     * Get the index name for the model.
     *
     * @return string
     */
    public function searchableAs()
    {
        return 'mix-search';
    }

    /**
     * The module object ID.
     *
     * @return string
     */
    public function getScoutKey()
    {
        return sprintf('%s>%s', ModelMorphMap::classToAliasName(static::class), $this->id);
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
        ];
    }

    /**
     * The model filter.
     * @return string
     */
    public function modelFilter()
    {
        return Filters\UserFilter::class;
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
