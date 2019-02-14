<?php

namespace App\Http\Controllers;

use App\Models\Talk;
use App\ModelMorphMap;
use App\Models\Comment;
use App\Models\UserExtra;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ListComments;
use App\Http\Requests\CreateComment;
use App\Http\Resources\Comment as CommentResource;

class CommentController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth')->only(['store', 'destroy']);
    }

    /**
     * Storage a comment.
     * @param \App\Http\Requests\CreateComment $request
     */
    public function store(CreateComment $request)
    {
        // Find commentable resource.
        $commentableClassnName = ModelMorphMap::aliasToClassName(
            $request->input('commentable_type')
        );
        $commentable = call_user_func(
            [$commentableClassnName, 'find'],
            $request->input('commentable_id')
        );

        // find user talk count model.
        $extra = $request->user()->extras()->firstOrCreate([
            'name' => 'comments_count',
            'type' => UserExtra::TYPE_INTEGER,
        ], [
            'integer_value' => 0,
        ]);

        // Make comment.
        $comment = new Comment(array_merge([
            'publisher_id' => $request->user()->id,
        ], $request->only([
            'content', 'resource_type', 'resource',
        ])));

        DB::transaction(function () use ($comment, $commentable, $extra) {
            $commentable->comments()->save($comment);
            $commentable->increment('comments_count', 1);
            $extra->increment('integer_value', 1);
        });

        return (new CommentResource($comment))
            ->toResponse($request)
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ListComments $request)
    {
        if ($request->query('id')) {
            $query = Comment::whereInId($request->query('id'));
        } elseif ($request->query('query')) {
            $query = Comment::search($request->query('query'));
        } else {
            $query = Comment::orderBy($request->query('sort', 'id'), $request->query('direction', 'desc'))
            ->when($request->query('publisher'), function ($query) use ($request) {
                return $query->wherePublisherId($request->query('publisher'));
            })
            ->when(! empty($request->only('commentable_type', 'commentable_id')), function ($query) use ($request) {
                return $this
                    ->whereCommentableType($request->query('commentable_type'))
                    ->whereCommentableId($request->query('commentable_id'));
            });
        }

        $comments = $query->paginate(10)->appends($request->query());
        if (empty($request->only('commentable_type', 'commentable_id'))) {
            $comments->load(['commentable']);
        }
        $comments->load(['publisher']);

        return CommentResource::collection($comments);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        // find user talk count model.
        $extra = $request->user()->extras()->firstOrCreate([
            'name' => 'comments_count',
            'type' => UserExtra::TYPE_INTEGER,
        ], [
            'integer_value' => 0,
        ]);
        DB::transaction(function () use ($extra, $comment) {
            $extra->decrement('integer_value', 1);
            $comment->commentable()->decrement('comments_count', 1);
            $comment->delete();
        });

        return $this->withHttpNoContent();
    }
}
