<?php

namespace App\Api\Controllers\User;

use Illuminate\Http\Request;
use App\Models\User as UserModel;
use App\Api\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Get Users.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function index(Request $request)
    {
        $limit = (int) $request->query('limit', 15);
        $offset = (int) $request->query('offset', 0);
        $name = $request->query('name');

        $users = UserModel::when($name, function ($query) use ($name) {
            return $query->where('name', 'LIKE', sprintf('%%%s%%', $name));
        })
            ->limit($limit)
            ->offset($offset)
            ->get();

        return response()->json($users, 200);
    }

    /**
     * Get user data.
     *
     * @param \App\Models\User $user
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function show(UserModel $user)
    {
        return response()->json($user, 200);
    }
}
