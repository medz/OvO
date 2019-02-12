<?php

declare(strict_types=1);

namespace App\Http\Resources;

class ForumNode extends JsonResource
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
        // $user = $request->user();
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'icon' => $this->whenStorageUrl($this->icon),
            'color' => $this->color,
            'counts' => [
                'threads' => $this->threads_count,
                'followers' => $this->followers_count,
            ],
        ];
    }
}
