<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForumThread extends Model
{
    use Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['publisher_id', 'node_id', 'title', 'last_comment_id', 'published_at', 'excellent_at', 'pinned_at'];

    /**
     * The thread content.
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function content(): HasOne
    {
        return $this->hasOne(ForumThreadContent::class, 'thread_id', 'id');
    }

    /**
     * The thread publisher.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'publisher_id', 'id');
    }

    /**
     * The thread node.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function node(): BelongsTo
    {
        return $this->belongsTo(ForumNode::class, 'node_id', 'id');
    }

    public function lastComment()
    {
        // return $this->hasOne()
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
            'node_id' => $this->node_id,
            'publisher_id' => $this->publisher_id,
            'title' => $this->title,
            'content' => $this->content->data ?? '',
        ];
    }
}
