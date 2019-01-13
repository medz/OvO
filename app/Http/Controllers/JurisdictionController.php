<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Jurisdiction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\Jurisdiction as JurisdictionResource;

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
        ]);
    }
}
