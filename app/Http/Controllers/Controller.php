<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function withHttpNoContent(): Response
    {
        return new Response('', Response::HTTP_NO_CONTENT);
    }

    protected function throwUnprocessableEntity(string $message)
    {
        throw new UnprocessableEntityHttpException($message);
    }
}
