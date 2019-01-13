<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Jurisdiction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\Jurisdiction as JurisdictionResource;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class JurisdictionController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get all nodes.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function nodes(Request $request): JsonResponse
    {
        return new JsonResponse([
            'all' => JurisdictionResource::collection(Jurisdiction::nodes()),
            'user' => JurisdictionResource::collection(
                $request->user()->jurisdictions
            ),
        ], Response::HTTP_OK);
    }

    /**
     * Attach a jurisdiction node to user.
     * @param \App\Models\User $user
     * @param string $node existed Jurisdiction::nodes
     * @throws \Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException
     * @return \Illuminate\Http\Response
     */
    public function attach(User $user, string $node): Response
    {
        $this->authorize('has', Jurisdiction::class);
        $illegalNode = ! Jurisdiction::nodes()->first(function (string $value) use ($node): bool {
            return $value === $node;
        });
        if ($illegalNode) {
            throw new UnprocessableEntityHttpException(
                trans('jurisdiction.node.illegal', [
                    'node' => $node,
                ])
            );
        } elseif (! $user->jurisdictions()->where('node', $node)->exists()) {
            $user->jurisdictions()->create([
                'node' => $node,
            ]);
        }

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * Detach a jurisdiction node to user.
     * @param \App\Models\User $user
     * @param string $node existed Jurisdiction::nodes
     * @return \Illuminate\Http\Response
     */
    public function detach(User $user, string $node): Response
    {
        $this->authorize('has', Jurisdiction::class);
        if ($user->jurisdictions()->where('node', $node)->exists()) {
            $user->jurisdictions()->where('node', $node)->delete();
        }

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
