<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Talk extends Model
{
    public const RESOURCE_TYPES = ['images', 'video', 'link'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'content', 'repostable_type', 'repostable_id',
        'resource_type', 'resource', 'cache',
    ];
}
