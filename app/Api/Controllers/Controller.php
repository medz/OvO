<?php

namespace App\Api\Controllers;

use Dingo\Api\Routing\Helpers;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
// use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

class Controller extends BaseController
{
    use DispatchesJobs, ValidatesRequests, Helpers;
}
