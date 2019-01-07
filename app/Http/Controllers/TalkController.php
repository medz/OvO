<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Talk;
use App\ModelMorphMap;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Talk as TalkResource;
use App\Http\Requests\CreateTalk as CreateTalkRequest;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class TalkController extends Controller
{
    /**
     * Create talk controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth')->only(['store']);
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
     * @param  \App\Http\Requests\CreateTalk  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateTalkRequest $request)
    {
        // Create a talk.
        $talk = new Talk($request->only(['content', 'resource_type', 'resource']));

        // Get repost resource class name.
        $repostableClassName = ModelMorphMap::aliasToClassName(
            (string) $repostableType = $request->input('repostable.type')
        );

        // Has repostable, and has repostable class exists.
        if ($request->has('repostable') && class_exists($repostableClassName)) {
            // Has repostable resource exists.
            if (! call_user_func([$repostableClassName, 'find'], $repostableId = $request->input('repostable.id'))) {
                throw new UnprocessableEntityHttpException('Repost resource not found.');
            }

            // Set repostable.
            $talk->repostable_type = $repostableType;
            $talk->repostable_id = $repostableId;
        }

        // find user talk count model.
        $talkCountModel = $request->user()->extras()->firstOrCreate(['name' => 'talks_count'], ['value' => 0]);

        // transaction
        DB::transaction(function () use ($talkCountModel, $talk) {
            $talk->save();
            $talkCountModel->increment('value', 1);
        });

        return (new TalkResource($talk))
            ->toResponse($request)
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Talk  $talk
     * @return \Illuminate\Http\Response
     */
    public function show(Talk $talk)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Talk  $talk
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Talk $talk)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Talk  $talk
     * @return \Illuminate\Http\Response
     */
    public function destroy(Talk $talk)
    {
        //
    }
}
