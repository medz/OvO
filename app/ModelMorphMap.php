<?php

declare(strict_types=1);

namespace App;

use Illuminate\Database\Eloquent\Relations\Relation;

class ModelMorphMap
{
    /**
     * Model morph maps.
     * @var array
     */
    protected static $map = [
        'users' => Models\User::class,
        'talks' => Models\Talk::class,
        'comments' => Models\Comment::class,
        'forum:nodes' => Models\ForumNode::class,
        'forum:threads' => Models\ForumThread::class,
    ];

    /**
     * Model alias name to class name.
     * @param string $className
     * @return null|string class name
     */
    public static function aliasToClassName(string $aliasName): ?string
    {
        return static::$map[$aliasName] ?? null;
    }

    /**
     * Model class name to alias name.
     * @param string $className
     * @return null|string alias name
     */
    public static function classToAliasName(string $className): ?string
    {
        if (($alias = array_search($className, static::map(), true)) === false) {
            return $alias;
        }

        return null;
    }

    /**
     * get all map.
     * @return array
     */
    public static function map(): array
    {
        return static::$map;
    }

    /**
     * Get all aliases.
     *
     * @return array
     */
    public static function classAliases(): array
    {
        return array_keys(static::$map);
    }

    /**
     * Morph map register.
     */
    public static function register(): void
    {
        Relation::morphMap(static::map(), true);
    }
}
