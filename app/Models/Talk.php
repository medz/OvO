<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Talk extends Model
{
    use Searchable;

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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'resource' => 'json',
    ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
        ];
    }

    /**
     * The talk of user.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The repostable.
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function repostable(): MorphTo
    {
        return $this->morphTo('repostable');
    }

    /**
     * The talk comments.
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
