<?php

namespace Medz\Fans\Models;

use Illuminate\Database\Eloquent\Model;

class PwWindIDSchool extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pw_windid_school';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'schoolid';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
