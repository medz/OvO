<?php

namespace App\Api\Controllers\Forum;

use Illuminate\Http\Request;
use App\Api\Controllers\Controller;
use App\Models\Forum as ForumModel;
use App\Models\ForumTopic as ForumTopicModel;

class TopicController extends Controller
{
    /**
     * Get forum topics.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Forum $forum
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function index(Request $request, ForumModel $forum)
    {
        return $this->all($request, $forum->topics());
    }

    /**
     * Get all forum topics.
     *
     * @param Request $request
     * @param Illuminate\Database\Eloquent\Builder|Illuminate\Database\Eloquent\Relations\Relation|null $query
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function all(Request $request, $query = null)
    {
        if (! $query) {
            $query = ForumTopicModel::query();
        }

        $limit = (int) $request->query('limit', 15);
        $offset = (int) $request->query('offset', 0);
        $name = $request->query('name');

        $topics = $query->when($name, function ($query) use ($name) {
            return $query->where('name', 'like', sprintf('%%%s%%', $name));
        })
            ->limit($limit)
            ->offset($offset)
            ->get();

        return response()->json($topics, 200);
    }
}
