<?php

namespace App\Http\Controllers;

use App\Models\ForumNode;
use App\Models\UserExtra;
use App\Models\ForumThread;
use Illuminate\Http\Request;
use App\Models\ForumThreadContent;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\CreateForumThread;

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
    public function index()
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ForumThread  $thread
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ForumThread $thread)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ForumThread  $thread
     * @return \Illuminate\Http\Response
     */
    public function destroy(ForumThread $thread)
    {
        //
    }

    public function transform(ForumNode $node, ForumThread $thread)
    {
        //
    }
}
