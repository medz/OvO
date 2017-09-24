<?php

namespace App\Api\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag as TagModel;

class TagController extends Controller
{
    /**
     * List tags.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function index(Request $request)
    {
        $limit = (int) $request->query('limit', 15);
        $offset = (int) $request->query('offset', 0);
        $name = $request->query('name');

        $tags = TagModel::when($name, function ($query) use ($name) {
            return $query->where('name', 'like', sprintf('%%%s%%', $name));
        })
            ->limit($limit)
            ->offset($offset)
            ->get();

        return response()->json($tags, 200);
    }
}
