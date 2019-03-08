<?php

namespace App\Models\Filters;

use App\Models\User;
use EloquentFilter\ModelFilter;

class UserFilter extends ModelFilter
{
    use Concerns\Authable;

    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function id(array $id)
    {
        foreach ($id as $item) {
            $this->whereId($item);
        }
    }

    /**
     * Filite the name.
     * @param string $name
     */
    public function name(string $name)
    {
        if ($this->auth('user:manage', User::class)) {
            return $this->whereLike(__METHOD__, $name);
        }
    }

    /**
     * Filite the email, Only manage user.
     * @param string $phone
     */
    public function email(string $email)
    {
        if ($this->auth('user:manage', User::class)) {
            return $this->whereLike(__METHOD__, $email);
        }
    }

    /**
     * Filite the phone, Only manage user.
     * @param string $phone
     */
    public function phone(string $phone)
    {
        if ($this->auth('user:manage', User::class)) {
            return $this->whereLike(__METHOD__, $phone);
        }
    }

    /**
     * Filite the itc, Only manage user.
     * @param string $itc
     */
    public function itc(string $itc)
    {
        if ($this->auth('user:manage', User::class)) {
            return $this->where('international_telephone_code', 'like', $itc);
        }
    }
}
