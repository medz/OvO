<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\User as UserModel;
use Illuminate\Support\Facades\Storage;

class UserExtra extends JsonResource
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
            'name' => $this->name,
            'value' => $this->value,
        ];
    }
}
