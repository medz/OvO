<?php

namespace App\Api\Controllers;

// use Illuminate\Http\Request;
use App\Models\TopicCategory as TopicCategoryModel;

class ForumCategoryController extends Controller
{
    /**
     * Show forum topic categories.
     *
     * Get a JSON representation of all the forum topic category.
     *
     * @param \App\Models\TopicCategory $model
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function index(TopicCategoryModel $model)
    {
        return $this->response()->array(
            $model->all()->toArray()
        )->setStatusCode(200);
    }

    /**
     * Show forum topic category.
     *
     * @param \App\Models\TopicCategory $model
     * @param int $category
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function show(TopicCategoryModel $model, int $category)
    {
        $category = $model->find($category);

        if (! $category) {
            return $this->response()->errorNotFound();
        }

        return $this->response()->array(
            $category->toArray()
        )->setStatusCode(200);
    }

    public function store()
    {
        // todo.
    }

    public function update()
    {
        // todo.
    }

    public function destroy()
    {
        // todo.
    }
}
