<?php
// app/Http/Resources/BayResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BayResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'greenhouse_id' => $this->greenhouse_id,
            'name' => $this->name,
            'description' => $this->description,
            'capacity' => $this->capacity,
            'greenhouse' => new GreenhouseResource($this->whenLoaded('greenhouse')),
            'job_entries_count' => $this->whenCounted('jobEntries'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}