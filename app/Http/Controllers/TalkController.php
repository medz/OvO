<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Talk;
use App\ModelMorphMap;
use App\Models\UserExtra;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Talk as TalkResource;
use App\Http\Requests\ListTalks as ListTalksRequest;
use App\Http\Requests\CreateTalk as CreateTalkRequest;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

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
        if ($request->query('id')) {
            return TalkResource::collection(
                Talk::whereInId($request->query('id'))
                    ->paginate(10)
                    ->appends($request->query())
            );
        } elseif ($request->query('query')) {
            return TalkResource::collection(
                Talk::search($request->query('query'))
                    ->paginate(10)
                    ->appends($request->query())
            );
        }

        return TalkResource::collection(
            Talk::orderBy($request->query('sort', 'id'), $request->query('direction', 'desc'))
                ->paginate(10)
                ->appends($request->query())
        );
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
        $talk = new Talk($request->only(['content', 'resource_type', 'resource']));
        $talk->publisher_id = $request->user()->id;

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
        $extra = $request->user()->extras()->firstOrCreate([
            'name' => 'talks_count',
            'type' => UserExtra::TYPE_INTEGER,
        ], [
            'integer_value' => 0,
        ]);

        // transaction
        DB::transaction(function () use ($extra, $talk) {
            $talk->save();
            $extra->increment('talks_count', 1);
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
        $talk->load(['publisher']);

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
            $extra = $request->user()->extras()->firstOrCreate([
                'name' => 'talks_count',
                'type' => UserExtra::TYPE_INTEGER,
            ], [
                'integer_value' => 0,
            ]);
        }

        // transaction
        DB::transaction(function () use ($extra, $talk) {
            $talk->delete();
            if ($extra && $extra->talks_count > 0) {
                $extra->decrement('integer_value', 1);
            }
        });

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
