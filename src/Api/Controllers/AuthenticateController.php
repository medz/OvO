<?php

namespace Medz\Fans\Api\Controllers;

use Illuminate\Http\Request;

class AuthenticateController extends Controller
{
    public function authenticate(Request $request)
    {
        dd($request->all());
    }
}
