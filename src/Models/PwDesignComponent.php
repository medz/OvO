<?php

namespace Medz\Wind\Models;

use Illuminate\Database\Eloquent\Model;

// use Illuminate\Database\Eloquent\Builder;

class PwDesignComponent extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pw_design_component';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'comp_id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
