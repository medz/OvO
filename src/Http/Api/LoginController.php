<?php

namespace Medz\Wind\Http\Api;

use Illuminate\Http\Request;
use Medz\Wind\Models\User;

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
