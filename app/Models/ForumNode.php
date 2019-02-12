<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumNode extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description', 'icon', 'color'];
}
