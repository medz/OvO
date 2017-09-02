<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Support\SinglePageAppcation;

class HomeController extends Controller
{
    /**
     * The web application.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Support\SinglePageAppcation $spa
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function index(Request $request, SinglePageAppcation $spa)
    {
        return $spa->applicable()
            ->toResponse($request);
    }
}
