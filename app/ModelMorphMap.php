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
    static protected $map = [
        'users' => Models\User::class,
        'talks' => Models\Talk::class,
    ];

    /**
     * Model alias name to class name.
     * @param string $className
     * @return null|string class name
     */
    static public function aliasToClassName(string $aliasName): ?string
    {
        return static::$map[$aliasName] ?? null;
    }

    /**
     * Model class name to alias name.
     * @param string $className
     * @return null|string alias name
     */
    static public function classToAliasName(string $className): ?string
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
    static public function map(): array
    {
        return static::$map;
    }

    /**
     * Get all aliases.
     * 
     * @return array
     */
    static public function classAliases(): array
    {
        return array_keys(static::$map);
    }

    /**
     * Morph map register.
     */
    static public function register(): void
    {
        Relation::morphMap(static::map(), true);
    }
}
