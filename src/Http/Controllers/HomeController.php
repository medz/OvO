<?php

namespace Medz\Fans\Http\Controllers;

use Illuminate\Http\Request;
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
    public function index(Request $request, ResponseFactoryContract $response, string $path = '/')
    {
        if (! $response->hasMacro('display')) {
            return $path !== '/'
                ? abort(404)
                : $response->view('welcome');
        }

        return $response->display($request, $response);
    }
}
