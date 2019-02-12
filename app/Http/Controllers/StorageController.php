<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;

class StorageController extends Controller
{
    const INPUT_KEY = 'file';

    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request)
    {
        $this->validate($request, $this->getValidateRules());
        $file = $request->file(static::INPUT_KEY);
        $filename = $file->store(
            $this->getStoragePath($file)
        );

        return new JsonResponse(['filename' => $filename], JsonResponse::HTTP_CREATED);
    }

    protected function getStoragePath(UploadedFile $file): string
    {
        list($type) = explode('/', $file->getMimeType());
        if (! $type) {
            $type = 'application';
        }

        return '-/'.$type.(new Carbon)->format('/Y/m/d/ga');
    }

    protected function getValidateRules(): array
    {
        return [
            static::INPUT_KEY => ['required', 'file', 'mimes:svg,jpeg,bmp,png,webp,gif,mp4,ogv,webm'],
        ];
    }
}
