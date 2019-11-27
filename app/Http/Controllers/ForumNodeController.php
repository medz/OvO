<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateForumNode;
use App\Http\Requests\DeleteForumNode;
use App\Http\Requests\UpdateForumNode;
use App\Http\Resources\ForumNode as ForumNodeResource;
use App\Models\ForumNode;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ForumNodeController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth')->only(['store', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ForumNodeResource::collection(
            ForumNode::all()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CreateForumNode  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateForumNode $request)
    {
        $this->authorize('has', ForumNode::class);
        $node = new ForumNode($request->only(['name', 'description', 'color', 'icon']));
        $node->save();

        return (new ForumNodeResource($node))
            ->toResponse($request)
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ForumNode  $node
     * @return \Illuminate\Http\Response
     */
    public function show(ForumNode $node)
    {
        return new ForumNodeResource($node);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ForumNode  $node
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateForumNode $request, ForumNode $node)
    {
        $this->authorize('has', ForumNode::class);
        $node->update($request->only(['name', 'description', 'color', 'icon']));

        return $this->withHttpNoContent();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ForumNode  $forumNode
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteForumNode $request, ForumNode $node)
    {
        $this->authorize('has', ForumNode::class);
        $target = ForumNode::whereId($request->input('node'))->firstOrFail();
        DB::transaction(function () use ($target, $node) {
            $target->increment('threads_count', $node->threads()->count());
            $node->threads()->update(['node_id', $target->id]);
        });

        return $this->withHttpNoContent();
    }
}
