<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Talk;
use App\Models\UserExtra;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Talk as TalkResource;
use App\Http\Requests\ListTalks as ListTalksRequest;
use App\Http\Requests\CreateTalk as CreateTalkRequest;

class TalkController extends Controller
{
    /**
     * Create talk controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth')->only(['store', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Http\Requests\ListTalks $request
     * @return mixed
     */
    public function index(ListTalksRequest $request)
    {
        $perPage = 10;
        $talks = Talk::filter($request->all())->paginateFilter($perPage);
        $talks->load(['lastComment', 'shareable']);

        return TalkResource::collection($talks);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CreateTalk  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateTalkRequest $request)
    {
        $this->authorize('create', Talk::class);

        // Create a talk.
        $talk = new Talk($request->only(['content', 'media']));
        $talk->publisher_id = $request->user()->id;

        if ($request->has('shareable')) {
            $talk->shareable_type = $request->shareable_type;
            $talk->shareable_id = $request->shareable_id;
        }

        // find user talk count model.
        $extra = $request->user()->extras()->firstOrCreate([
            'name' => 'talks_count',
            'value_type' => UserExtra::TYPE_INTEGER,
        ], [
            'integer_value' => 0,
        ]);

        // transaction
        DB::transaction(function () use ($extra, $talk) {
            $talk->save();
            $extra->increment('integer_value', 1);
        });

        return (new TalkResource($talk))
            ->toResponse($request)
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Talk  $talk
     * @return mixed
     */
    public function show(Talk $talk)
    {
        $talk->load(['publisher', 'shareable']);
        $talk->increment('views_count', 1);

        return new TalkResource($talk);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Talk  $talk
     * @return \Illuminate\Http\Response
     */
    public function destroy(Talk $talk)
    {
        $this->authorize('delete', $talk);
        $extra = null;
        if ($talk->publisher) {
            $extra = $talk->publisher->extras()->firstOrCreate([
                'name' => 'talks_count',
                'value_type' => UserExtra::TYPE_INTEGER,
            ], [
                'integer_value' => 0,
            ]);
        }

        // transaction
        DB::transaction(function () use ($extra, $talk) {
            $talk->delete();
            if ($extra && $extra->integer_value > 0) {
                $extra->decrement('integer_value', 1);
            }
        });

        return $this->withHttpNoContent();
    }
}
