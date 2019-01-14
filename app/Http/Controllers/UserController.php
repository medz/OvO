<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\User as UserResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // return 
    }

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
