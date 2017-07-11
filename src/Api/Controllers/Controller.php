<?php

namespace Medz\Fans\Api\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
// use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Dingo\Api\Routing\Helpers;

class Controller extends BaseController
{
    use DispatchesJobs, ValidatesRequests, Helpers;
}
