<?php

namespace Medz\Wind\Models;

use Illuminate\Database\Eloquent\Model;

class PwWindIDArea extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pw_windid_area';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'areaid';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
