<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\User as UserResource;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Get user list.
     * @param \Illuminate\Http\Reques $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $perPage = 10;

        return UserResource::collection(
            User::filter($request->all())->paginateFilter($perPage)
        );
    }

    /**
     * Get a user info.
     * @param \Illuminate\Http\Reques $request
     * @param null\App\Models\User $user
     * @return mixed
     */
    public function show(Request $request, ?User $user = null)
    {
        if ($user === null && ! ($user = $request->user())) {
            $exception = new ModelNotFoundException;
            $exception->setModel(User::class);
            throw $exception;
        }
        $user->load(['extras']);

        return new UserResource($user);
    }
}
