<?php

namespace App\Api\Controllers\User;

use Illuminate\Http\Request;
use App\Api\Controllers\Controller;

class AuthenticatedController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        return response()->json(array_merge($user->toArray(), [
            'phone' => $user->phone,
            'email' => $user->email,
        ]), 200);
    }
}
