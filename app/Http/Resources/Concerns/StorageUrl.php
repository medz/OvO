<?php

declare(strict_types=1);

namespace App\Http\Resources\Concerns;

use Illuminate\Support\Facades\Storage;

trait StorageUrl
{
    protected function whenStorageUrl(?string $path = null): ?string
    {
        if (! is_string($path)) {
            return null;
        }

        return $this->when($path, function () use ($path) {
            if (preg_match('/https?\:\/\//', $path)) {
                return $path;
            }

            return Storage::url($path);
        });
    }
}
