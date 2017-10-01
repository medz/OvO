<?php

namespace App\Api\Controllers\Forum;

use Closure;
use Illuminate\Http\Request;
use App\Api\Controllers\Controller;
use App\Models\Forum as ForumModel;
use Illuminate\Validation\Validator;
use App\Models\ForumTopic as ForumTopicModel;
use App\Api\Requests\StoreForumTopic as StoreForumTopicRequest;

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
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($topics, 200);
    }

    /**
     * Get forum topic info.
     *
     * @param \App\Models\ForumTopic $topic
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function show(ForumTopicModel $topic)
    {
        return response()->json($topic, 200);
    }

    /**
     * Create a topic.
     *
     * @param \App\Api\Requests\StoreForumTopic $request
     * @param \App\Models\Forum $forum
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function store(StoreForumTopicRequest $request, ForumModel $forum)
    {
        $category = $request->input('category', null);
        if (! $forum->allow_not_category || $category) {
            $request->validateCategory(function (Validator $validator, Closure $failedValidation)
            use (&$category, $forum) {
                $validator->errors()->add('category', '选择的分类不存在');
                $category = $forum->categories()
                    ->where('id', $category)
                    ->firstOr($failedValidation);
            });
        }

        $topic = new ForumTopicModel();
        $topic->forum_topic_categories_id = $category->id ?? null;
        $topic->user_id = $request->user()->id;
        $topic->subject = $request->input('subject');
        $topic->body = $request->input('body');

        $forum->topics()->save($topic);
        $forum->increment('topic_count', 1);
        if (! is_null($category)) {
            $category->increment('topic_count', 1);
        }

        return response()->json([
            'message' => '创建成功',
            'topic_id' => $topic->id
        ], 201);
    }
}
