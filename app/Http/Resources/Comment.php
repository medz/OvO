<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\ModelMorphMap;
use App\Models\Talk as ForumThreadModel;
use App\Models\Talk as TalkModel;
use Illuminate\Http\Resources\MissingValue;

class Comment extends JsonResource
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
            'publisher_id' => $this->publisher_id,
            'content' => $this->connent,
            'pinned_at' => $this->whenDateToZulu($this->pinned_at),
            'created_at' => $this->whenDateToZulu($this->created_at),
            'commentable' => [
                'type' => $this->commentable_type,
                'id' => $this->commentable_id,
                $this->whenLoaded('commentable', function () {
                    return $this->merge(function () {
                        switch (ModelMorphMap::aliasToClassName($this->commentable_type)) {
                            case TalkModel::class:
                                return ['talk' => new Talk($this->commentable)];
                            case ForumThreadModel::class:
                                return ['forum:thread' => new ForumThread($this->commentable)];
                            default:
                                return new MissingValue;
                        }
                    });
                }),
            ],
            'resource' => [
                'type' => $this->resource_type,
                'video' => $this->when($this->resource_type === 'video', function () {
                    return $this->whenStorageUrl($this->resource);
                }),
                'images' => $this->when($this->resource_type === 'images', function () {
                    return array_map(function ($path) {
                        return $this->whenStorageUrl($path);
                    }, $this->resource);
                }),
                'text' => $this->when($this->resource_type === 'long-text', function () {
                    return $this->resource;
                }),
            ],
            $this->whenLoaded('publisher', function () {
                return $this->merge([
                    'publisher' => new User($this->publisher),
                ]);
            }),
        ];
    }
}
