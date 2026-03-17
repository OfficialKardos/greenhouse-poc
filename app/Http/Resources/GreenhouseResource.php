<?php
// app/Http/Resources/GreenhouseResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GreenhouseResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'zip' => $this->zip,
            'full_address' => $this->full_address,
            'bays' => BayResource::collection($this->whenLoaded('bays')),
            'bays_count' => $this->whenCounted('bays'),
            'job_entries_count' => $this->whenCounted('jobEntries'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
}