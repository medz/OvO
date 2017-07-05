<?php

namespace Medz\Fans\Models;

use Illuminate\Database\Eloquent\Model;

class PwUserGroup extends Model
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'gid';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
