<?php

declare(strict_types=1);

namespace App\Api\Controllers;

use App\Models\User as UserModel;
use Illuminate\Http\JsonResponse;
use App\Api\Requests\ResgisterUser as RegisterRequest;

class UserRegisterController extends Controller
{
    /**
     * User register.
     *
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function __invoke(RegisterRequest $request): JsonResponse
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
