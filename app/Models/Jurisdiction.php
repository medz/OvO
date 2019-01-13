<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class Jurisdiction extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'node'];

    /**
     * Cached Jurisdiction nodes.
     * @var \Illuminate\Support\Collection
     */
    static protected $cachedJurisdictions;

    /**
     * Get all Jurisdiction nodes.
     * @return \Illuminate\Support\Collection
     */
    static public function nodes(): Collection
    {
        if (static::$cachedJurisdictions instanceof Collection) {
            return static::$cachedJurisdictions;
        }

        return static::$cachedJurisdictions = new Collection(
            json_decode(file_get_contents(resource_path('jurisdictions.json')), true)
        );
    }
}
