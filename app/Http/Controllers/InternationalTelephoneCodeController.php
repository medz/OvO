<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Models\InternationalTelephoneCode;
use App\Http\Requests\CreateTTC as CreateTTCRequest;
use App\Http\Requests\UpdateTTC as UpdateTTCRequest;
use App\Http\Resources\InternationalTelephoneCode as InternationalTelephoneCodeResource;

class InternationalTelephoneCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request      $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return InternationalTelephoneCodeResource::collection(InternationalTelephoneCode::all())
            ->toResponse($request)
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CreateTTC  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateTTCRequest $request): JsonResponse
    {
        $ttc = new InternationalTelephoneCode($request->only(['code', 'name', 'icon']));
        if ($request->input('enabled', false)) {
            $ttc->enabled_at = new Carbon;
        }
        $ttc->save();

        return (new InternationalTelephoneCodeResource($ttc))
            ->toResponse($request)
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTTC  $request
     * @param  App\Models\InternationalTelephoneCode $ttc
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTTCRequest $request, InternationalTelephoneCode $ttc): Response
    {
        foreach ($request->only(['code', 'name', 'icon']) as $key => $value) {
            if ($request->has($key)) {
                $ttc->{$key} = $value;
                $needSave = true;
            }
        }
        if (($enabled = $request->input('enabled')) === true || $enabled === 1) {
            $ttc->enabled_at = new Carbon;
        } elseif ($enabled === false || $enabled === 0) {
            $ttc->enabled_at = null;
        }
        $ttc->save();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
