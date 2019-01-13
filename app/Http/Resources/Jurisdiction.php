<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Jurisdiction as JurisdictionModel;

class Jurisdiction extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $node = $this->resource;
        if ($node instanceof JurisdictionModel) {
            $node = $node->node;
        }

        return [
            'node' => $node,
            'name' => trans(sprintf('jurisdiction.%s.name', $node)),
            'desc' => trans(sprintf('jurisdiction.%s.desc', $node)),
        ];
    }
}
