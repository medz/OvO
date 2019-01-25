<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    /**
     * The talk of user.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
