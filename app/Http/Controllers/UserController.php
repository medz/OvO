<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\User as UserResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

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
        if ($request->query('id')) {
            return UserResource::collection(
                User::whereIn('id', $request->query('id'))
                    ->paginate($perPage)
                    ->appends($request->query())
            );
        } elseif ($request->query('query')) {
            return UserResource::collection(
                User::search($request->query('query'))
                    ->paginate($perPage)
                    ->appends($request->query())
            );
        }

        return UserResource::collection(
            User::orderBy($request->query('sort', 'id'), $request->query('direction', 'desc'))
                ->paginate($perPage)
                ->appends($request->query())
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
