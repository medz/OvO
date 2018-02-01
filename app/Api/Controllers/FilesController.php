<?php

declare(strict_types=1);

namespace App\Api\Controllers;

class FilesController extends Controller
{
    /**
     * Create the file controller instance.
     *
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function __constrcut()
    {
        $this->middleware('auth:api');
    }
}
