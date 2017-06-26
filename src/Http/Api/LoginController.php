<?php

namespace Medz\Wind\Http\Api;

use Medz\Wind\Models\User;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $name = $request->input('name');
        $password = $request->input('password');

        $user = User::find(1);

        return $user->createToken('a', ['*']);
    }
}
