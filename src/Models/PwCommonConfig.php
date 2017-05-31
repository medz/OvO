<?php

namespace Medz\Wind\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class PwCommonConfig extends Model
{
    /**
     * The primary key for the model.
     *
     * @var array
     */
    protected $primaryKey = ['name', 'namespace'];
    protected $fillable = ['name', 'namespace', 'value', 'vtype', 'description'];
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pw_common_config';

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
