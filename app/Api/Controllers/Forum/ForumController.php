<?php

namespace App\Api\Controllers\Forum;

use Illuminate\Http\Request;
use App\Api\Controllers\Controller;
use App\Models\Forum as ForumModel;

class ForumController extends Controller
{
    /**
     * List all forums.
     *
     * @param Illuminate\Http\Request $request
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function index(Request $request)
    {
        $limit = (int) $request->query('limit', 15);
        $offset = (int) $request->query('offset', 0);
        $name = $request->query('name');

        $forums = ForumModel::when($name, function ($query) use ($name) {
            return $query->where('name', 'like', sprintf('%%%s%%', $name));
        })
            ->limit($limit)
            ->offset($offset)
            ->get();

        return response()->json($forums, 200);
    }
}
