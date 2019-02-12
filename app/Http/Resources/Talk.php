<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\ModelMorphMap;
use App\Models\Talk as TalkModel;
use App\Models\User as UserModel;
use Illuminate\Http\Resources\MissingValue;

class Talk extends JsonResource
{
    use Concerns\StorageUrl;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'publisher_id' => $this->publisher_id,
            'publisher' => $this->whenLoaded('publisher', function () {
                return new User($this->publisher);
            }),
            'resource' => [
                'type' => $this->resource_type,
                'link' => $this->when($this->resource_type === 'link', function () {
                    return $this->resource;
                }),
                'video' => $this->when($this->resource_type === 'video', function () {
                    return $this->whenStorageUrl($this->resource);
                }),
                'images' => $this->when($this->resource_type === 'images', function () {
                    return array_map(function ($path) {
                        return $this->whenStorageUrl($path);
                    }, $this->resource);
                }),
            ],
            'repostable' => [
                'type' => $this->repostable_type,
                'id' => $this->repostable_id,
                $this->whenLoaded('repostable', function () {
                    return $this->merge(function () {
                        switch (ModelMorphMap::aliasToClassName($this->repostable_type)) {
                            case TalkModel::class:
                                return ['talk' => new static($this->repostable)];
                            case UserModel::class:
                                return ['user' => new User($this->repostable)];
                            default:
                                return new MissingValue();
                        }
                    });
                }),
            ],
        ];
    }
}
