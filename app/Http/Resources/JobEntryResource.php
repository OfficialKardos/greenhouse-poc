<?php
// app/Http/Resources/JobEntryResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JobEntryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'job_number' => $this->job_number,
            'user' => new UserResource($this->whenLoaded('user')),
            'greenhouse' => new GreenhouseResource($this->whenLoaded('greenhouse')),
            'bay' => new BayResource($this->whenLoaded('bay')),
            'job_type' => new JobTypeResource($this->whenLoaded('jobType')),
            'status' => $this->status,
            'notes' => $this->notes,
            'values' => JobEntryValueResource::collection($this->whenLoaded('values')),
            'photos' => JobPhotoResource::collection($this->whenLoaded('photos')),
            'metadata' => $this->metadata,
            'submitted_at' => $this->submitted_at,
            'approved_by' => new UserResource($this->whenLoaded('approver')),
            'approved_at' => $this->approved_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}