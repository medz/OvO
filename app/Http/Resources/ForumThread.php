<?php

declare(strict_types=1);

namespace App\Http\Resources;

class ForumThread extends JsonResource
{
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
            'node_id' => $this->node_id,
            'state' => [
                'published' => (bool) $this->published_at,
                'excellent' => (bool) $this->excellent_at,
                'pinned' => (bool) $this->pinned_at,
            ],
            'counts' => [
                'views' => $this->views_count,
                'likes' => $this->likes_count,
                'comments' => $this->comments_count,
            ],
            'created_at' => $this->whenDateToZulu($this->created_at),
            'title' => $this->title,
            $this->whenLoaded('content', function () {
                return $this->merge([
                    'content' => $this->content->data,
                ]);
            }),
            $this->whenLoaded('publisher', function () {
                return $this->merge([
                    'publisher' => new User($this->publisher),
                ]);
            }),
            $this->whenLoaded('node', function () {
                return $this->merge([
                    'node' => new ForumNode($this->node),
                ]);
            }),
        ];
    }
}
