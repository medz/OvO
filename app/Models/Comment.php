<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Laravel\Scout\Searchable;

class Comment extends Model
{
    use Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['publisher_id', 'pinned_at', 'resource_type', 'resource', 'content'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'resource' => 'json',
    ];

    /**
     * The commentable.
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function commentable(): MorphTo
    {
        return $this->morphTo('commentable');
    }

    /**
     * The comment publisher.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'publisher_id', 'id');
    }

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
            'publisher_id' => $this->publisher_id,
        ];
    }
}
