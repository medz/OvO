<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserExtra extends Model
{
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
}
