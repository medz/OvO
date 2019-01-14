<?php

declare(strict_types=1);

namespace App\Http\Resources;

class InternationalTelephoneCode extends JsonResource
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
            'code' => $this->code,
            'name' => $this->name,
            'icon' => $this->icon,
            'enabled' => boolval($this->enabled_at),
        ];
    }
}
