<?php
// app/Http/Resources/DropdownListResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DropdownListResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'values' => DropdownValueResource::collection($this->whenLoaded('values')),
            'values_count' => $this->whenCounted('values'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}