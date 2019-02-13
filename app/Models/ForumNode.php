<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ForumNode extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description', 'icon', 'color'];

    /**
     * The node threads.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function threads(): HasMany
    {
        return $this->hasMany(ForumThread::class, 'node_id', 'id');
    }
}
