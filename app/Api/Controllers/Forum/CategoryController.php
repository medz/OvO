<?php

namespace App\Api\Controllers\Forum;

use Illuminate\Http\Request;
use App\Api\Controllers\Controller;
use App\Models\Forum as ForumModel;
use App\Models\ForumTopicCategory as ForumTopicCategoryModel;

class CategoryController extends Controller
{
    /**
     * List forum all categories.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Forum $forum
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function index(Request $request, ForumModel $forum)
    {
        $limit = (int) $request->query('limit', 15);
        $offset = (int) $request->query('offset', 0);
        $name = $request->query('name');

        $categories = $forum->categories()
            ->when($name, function ($query) use ($name) {
                return $query->where('name', 'like', sprintf('%%%s%%', $name));
            })
            ->limit($limit)
            ->offset($offset)
            ->get();

        return response()->json($categories, 200);
    }

    /**
     * Get forum category infi.
     *
     * @param \App\Models\ForumTopicCategory $category
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function show(ForumTopicCategoryModel $category)
    {
        return response()->json($category, 200);
    }
}
