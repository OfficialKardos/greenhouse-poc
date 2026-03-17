<?php
// app/Http/Resources/JobEntryValueResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JobEntryValueResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'job_field' => new JobFieldResource($this->whenLoaded('jobField')),
            'value' => $this->value,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}