<?php
// app/Http/Resources/JobTypeResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JobTypeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'icon' => $this->icon,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            'fields' => JobFieldResource::collection($this->whenLoaded('fields')),
            'fields_count' => $this->whenCounted('fields'),
            'job_entries_count' => $this->whenCounted('jobEntries'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}