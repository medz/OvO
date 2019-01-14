<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InternationalTelephoneCode extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['code', 'name', 'icon', 'enabled_at'];
}
