<?php

namespace App\Api\Controllers\Auth;

use App\Models\User as UserModel;
use App\Api\Controllers\Controller;
use App\Api\Requests\ResgisterUser as RegisterRequest;

class RegisterController extends Controller
{
    /**
     * Register user.
     *
     * @param \App\Api\Requests\ResgisterUser $request
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function store(RegisterRequest $request)
    {
        $user = new UserModel;
        $user->login = $request->input('login');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));

        $user->save();
        $user->roles()->attach(
            config('user.register.role')
        );

        return response('', 204);
    }
}
