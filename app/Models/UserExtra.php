<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserExtra extends Model
{
    const TYPE_INTEGER = 'integer';
    const TYPE_JSON = 'json';
    const TYPE_STRING = 'string';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'name', 'value'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'value' => 'json',
    ];
}
