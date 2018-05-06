<?php

declare(strict_types=1);

namespace App\Api\Controllers;

use App\Api\Requests\UploadFileRequest;

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

    /**
     * Upload file method.
     *
     * @param \App\Api\Requests\UploadFileRequest $request
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function store(UploadFileRequest $request)
    {
        // TODO.
    }
}
