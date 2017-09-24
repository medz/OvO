<?php

namespace App\Api\Controllers\Forum;

use Illuminate\Http\Request;
use App\Api\Controllers\Controller;
use App\Models\Forum as ForumModel;

class TopicController extends Controller
{
    public function index(Request $request, ForumModel $forum)
    {
        $limit = (int) $request->query('limit', 15);
        $offset = (int) $request->query('offset', 0);
        $name = $request->query('name');

        $topics = $forum->topics()
            ->when($name, function ($query) use ($name) {
                return $query->where('name', 'like', sprintf('%%%s%%', $name));
            })
            ->limit($limit)
            ->offset($offset)
            ->get();

        return response()->json($topics, 200);
    }
}
