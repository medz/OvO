<?php

namespace Medz\Wind\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class PwWindIDConfig extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pw_windid_config';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = ['name', 'namespace'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Set the keys for a save update query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setKeysForSaveQuery(Builder $query)
    {
        foreach ($this->getKeyName() as $key) {
            $query->where($key, '=', $this->original[$key] ?? $this->getAttribute($key));
        }

        return $query;
    }
}
