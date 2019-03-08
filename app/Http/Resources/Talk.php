<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\ModelMorphMap;
use App\Models\Talk as TalkModel;
use App\Models\Talk as ForumThreadModel;
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
            'publisher_id' => $this->publisher_id,
            'content' => $this->content,
            'media' => $this->when($this->media, $this->media),
            $this->mergeWhen($this->shareable_type && $this->shareable_id, function () {
                return [
                    'type' => $this->shareable_type,
                    'id' => $this->shareable_id,
                    $this->whenLoaded('shareable', function () {
                        return $this->merge(function () {
                            switch (ModelMorphMap::aliasToClassName($this->shareable_type)) {
                                case TalkModel::class:
                                    return ['talk' => new static($this->shareable)];
                                case ForumThreadModel::class:
                                    return ['forum:thread' => new ForumThread($this->shareable)];
                                default:
                                    return new MissingValue();
                            }
                        });
                    }),
                ];
            }),
            'created_at' => $this->whenDateToZulu($this->created_at),
            'counts' => [
                'views' => $this->views_count,
                'likes' => $this->likes_count,
                'comments' => $this->comments_count,
                'shares' => $this->shares_count,
            ],
        ];
    }
}
