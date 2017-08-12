<?php

namespace Medz\Fans\Http\Controllers;

use Illuminate\Http\Request;
use Medz\Fans\Support\SinglePageAppcation;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseFactoryContract;

class HomeController extends Controller
{
    /**
     * The web application.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Routing\ResponseFactory $response
     * @param string $path
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function index(Request $request, SinglePageAppcation $spa)
    {
        $applicable = $spa->applicable();

        return $applicable->display($request);
    }
}
