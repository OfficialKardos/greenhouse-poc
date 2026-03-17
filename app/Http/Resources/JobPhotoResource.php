<?php
// app/Http/Resources/JobPhotoResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JobPhotoResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'job_field_id' => $this->job_field_id,
            'filename' => $this->filename,
            'url' => $this->url,
            'thumbnail_url' => $this->thumbnail_url,
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at
        ];
    }
}