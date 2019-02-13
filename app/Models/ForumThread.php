<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content->data ?? '',
        ];
    }
}
