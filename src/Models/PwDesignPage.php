<?php

namespace Medz\Fans\Models;

use Illuminate\Database\Eloquent\Model;

// use Illuminate\Database\Eloquent\Builder;

class PwDesignPage extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pw_design_page';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'page_id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
