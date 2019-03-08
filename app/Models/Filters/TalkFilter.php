<?php

namespace App\Models\Filters;

use EloquentFilter\ModelFilter;

class TalkFilter extends ModelFilter
{
    use Concerns\Authable;

    /**
     * Related Models that have ModelFilters as well as the method on the ModelFilter
     * As [relationMethod => [input_key1, input_key2]].
     *
     * @var array
     */
    public $relations = [];

    public function publisher(array $ids)
    {
        foreach ($ids as $id) {
            $this->wherePublisherId($id);
        }
    }

    public function id(array $ids)
    {
        foreach ($ids as $id) {
            $this->whereId($id);
        }
    }
}
