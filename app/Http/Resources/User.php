<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\User as UserModel;
use Illuminate\Support\Facades\Storage;

class User extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = $request->user();
        return [
            'id' => $this->id,
            'name' => $this->when($this->name, $this->name),
            'avatar' => $this->when($this->avatar, function () {
                if (preg_match('/https?\:\/\//', $this->avatar)) {
                    return $this->avatar;
                }

                return Storage::url($this->avatar);
            }),
            $this->mergeWhen($user instanceof UserModel && $user->id === $this->id, function () {
                return [
                    'phone' => [
                        'number' => $this->phone,
                        'international_telephone_code' => $this->international_telephone_code,
                        'verified_at' => $this->whenDateToZulu($this->phone_verified_at),
                    ],
                    'email' => $this->when($this->email, function () {
                        return [
                            'address' => $this->email,
                            'verified_at' => $this->whenDateToZulu($this->email_verified_at)
                        ];
                    })
                ];
            }),
            'created_at' => $this->whenDateToZulu($this->created_at),
            'extras' => UserExtra::collection($this->whenLoaded('extras')),
        ];
    }
}
