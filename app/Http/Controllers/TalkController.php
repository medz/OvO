<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Talk;
use App\ModelMorphMap;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\Filter;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;
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
        $this->middleware('auth')->only(['store', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $talks = QueryBuilder::for(Talk::class, $request)
            ->allowedFilters([
                Filter::exact('id'),
                Filter::exact('publisher_id'),
                Filter::partial('content'),
            ])
            ->allowedFields([
                'id', 'publisher_id', 'content',
                'resource_type', 'resource', 'cache',
            ])
            ->allowedSorts('id')
            ->defaultSort('-id')
            ->allowedIncludes([
                'publisher',
                'publisher.extras',
            ])
            ->paginate(10)
            ->appends($request->query());

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
        $talkCountModel = $request->user()->extras()->firstOrCreate(['name' => 'talks_count'], ['value' => 0]);

        // transaction
        DB::transaction(function () use ($talkCountModel, $talk) {
            $talk->save();
            $talkCountModel->update([
                'value' => $talkCountModel->value + 1,
            ]);
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
        $talkCountModel = null;
        if ($talk->publisher) {
            $talkCountModel = $talk->publisher->extras()->firstOrCreate(['name' => 'talks_count'], ['value' => ['count' => 0]]);
        }

        // transaction
        DB::transaction(function () use ($talkCountModel, $talk) {
            $talk->delete();
            if ($talkCountModel && $talkCountModel->value['count'] > 0) {
                $talkCountModel->update([
                    'value' => $talkCountModel->value - 1,
                ]);
            }
        });

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
