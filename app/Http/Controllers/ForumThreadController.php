<?php

namespace App\Http\Controllers;

use App\Models\ForumNode;
use App\Models\UserExtra;
use App\Models\ForumThread;
use Illuminate\Http\Request;
use App\Models\ForumThreadContent;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ListForumThreads;
use App\Http\Requests\CreateForumThread;
use App\Http\Requests\UpdateForumThread;
use App\Http\Resources\ForumThread as ForumThreadResource;

class ForumThreadController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this
            ->middleware('auth')
            ->only(['store', 'update', 'destroy', 'transform']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ListForumThreads $request)
    {
        if ($request->query('id')) {
            return ForumThreadResource::collection(
                ForumThread::whereInId($request->query('id'))
                    ->paginate(10)
                    ->appends($request->query())
            );
        } elseif ($request->query('query')) {
            return ForumThreadResource::collection(
                ForumThread::search($request->query('query'))
                    ->paginate(10)
                    ->appends($request->query())
            );
        }

        return ForumThreadResource::collection(
            ForumThread::orderBy($request->query('sort', 'id'), $request->query('direction', 'desc'))
                ->when($request->query('node'), function ($query) use ($request) {
                    return $query->whereNodeId($request->query('node'));
                })
                ->when($request->query('publisher'), function ($query) use ($request) {
                    return $query->wherePublisherId($request->query('publisher'));
                })
                ->paginate(10)
                ->appends($request->query())
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CreateForumThread  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ForumNode $node, CreateForumThread $request)
    {
        // Create a forum thread model.
        $thread = new ForumThread([
            'title' => $request->input('title'),
            'node_id' => $node->id,
        ]);

        // find user talk count model.
        $extra = $request->user()->extras()->firstOrCreate([
            'name' => 'forum_threads_count',
            'type' => UserExtra::TYPE_INTEGER,
        ], [
            'integer_value' => 0,
        ]);

        DB::transaction(function () use ($extra, $thread, $node, $request) {
            // Saved the publisher thread.
            $request->user()->forumThreads()->save($thread);

            // Increment publisher forum threads count.
            $extra->increment('integer_value', 1);

            // If sent thread content, Create a thread
            // content model, And save, Append relationship
            // to thread.
            if ($request->input('content')) {
                $thread->content()->save(new ForumThreadContent([
                    'data' => $request->input('content'),
                ]));
            }

            // Increment node threads count
            $node->increment('threads_count', 1);
        });

        return $this->withHttpNoContent();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ForumThread  $thread
     * @return \Illuminate\Http\Response
     */
    public function show(ForumThread $thread)
    {
        $thread->load(['content', 'publisher', 'node']);

        return new ForumThreadResource($thread);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateForumThread  $request
     * @param  \App\Models\ForumThread  $thread
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateForumThread $request, ForumThread $thread)
    {
        if (empty($payload = $request->only(['title', 'content']))) {
            return $this->withHttpNoContent();
        }
        $thread->load(['content']);

        DB::transaction(function () use ($payload, $thread) {
            // If sent title change content.
            if ($payload['title']) {
                $thread->update(['title' => $payload['title']]);
            }

            // If sent content change.
            if ($payload['content'] && $thread->content instanceof ForumThreadContent) {
                $thread->content()->update(['data' => $payload['content']]);
            } elseif ($payload['content']) {
                $thread->content()->save(new ForumThreadContent([
                    'data' => $payload['content'],
                ]));
            }
        });

        return $this->withHttpNoContent();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ForumThread  $thread
     * @return \Illuminate\Http\Response
     */
    public function destroy(ForumThread $thread)
    {
        // find user talk count model.
        $extra = $request->user()->extras()->firstOrCreate([
            'name' => 'forum_threads_count',
            'type' => UserExtra::TYPE_INTEGER,
        ], [
            'integer_value' => 0,
        ]);
        DB::transaction(function () use ($extra, $thread) {
            $extra->decrement('integer_value', 1);
            $thread->node()->decrement('threads_count', 1);
            $thread->content()->delete();
            $thread->delete();
        });

        return $this->withHttpNoContent();
    }

    /**
     * Transform thread node to selected node.
     */
    public function transform(ForumNode $node, ForumThread $thread)
    {
        if ($node->id === $thread->node_id) {
            return $this->withHttpNoContent();
        }

        DB::transaction(function () use ($node, $thread) {
            $node->increment('threads_count', 1);
            $thread->node()->decrement('threads_count', 1);
            $thread->update(['node_id' => $node->id]);
        });

        return $this->withHttpNoContent();
    }
}
