<?php

namespace App\Contracts;

interface Applicable
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function toResponse($request);
}
