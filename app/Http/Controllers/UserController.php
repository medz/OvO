<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Resources\User as UserResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    /**
     * Get user list.
     * @param \Illuminate\Http\Reques $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $users = QueryBuilder::for(User::class, $request)
            ->allowedFilters([
                Filter::exact('id'),
                Filter::partial('name'),
                Filter::exact('international_telephone_code'),
                Filter::partial('phone'),
                Filter::exact('phone_verified_at'),
                Filter::partial('email'),
                Filter::partial('email_verified_at'),
            ])
            ->allowedFields([
                'id', 'name', 'avatar', 'phone',
                'international_telephone_code',
                'phone_verified_at', 'email',
                'email_verified_at', 'created_at',
            ])
            ->allowedSorts('id')
            ->defaultSort('-id')
            ->allowedIncludes('extras')
            ->paginate(10)
            ->appends($request->query());

        return UserResource::collection($users);
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
