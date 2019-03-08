<?php

namespace App\Models;

use App\ModelMorphMap;
use Laravel\Scout\Searchable;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Talk extends Model
{
    use Searchable;
    use Filterable;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'publisher_id', 'content',
        'shareable_type', 'shareable_id',
        'last_comment_id', 'media', 'views_count',
        'views_count', 'likes_count',
        'comments_count', 'shares_count',
        'created_at', 'updated_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'media' => 'array',
    ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'content' => $this->content,
        ];
    }

    /**
     * Get the index name for the model.
     *
     * @return string
     */
    public function searchableAs()
    {
        return 'mix-search';
    }

    /**
     * The module object ID.
     *
     * @return string
     */
    public function getScoutKey()
    {
        return sprintf('%s>%s', ModelMorphMap::classToAliasName(static::class), $this->id);
    }

    /**
     * The model filter.
     * @return string
     */
    public function modelFilter()
    {
        return Filters\TalkFilter::class;
    }

    /**
     * The talk of user.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'publisher_id', 'id');
    }

    /**
     * The talk comments.
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * The talk last comment.
     */
    public function lastComment(): HasOne
    {
        return $this->hasOne(Comment::class, 'id', 'last_comment_id');
    }

    /**
     * The talk share resource.
     */
    public function shareable(): MorphTo
    {
        return $this->morphTo('shareable');
    }
}
