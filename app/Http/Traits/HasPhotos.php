<?php
// app/Traits/HasPhotos.php

namespace App\Traits;

use App\Models\JobPhoto;
use Illuminate\Support\Facades\Storage;

trait HasPhotos
{
    public function photos()
    {
        return $this->morphMany(JobPhoto::class, 'photoable');
    }

    public function addPhoto($file, $fieldId = null)
    {
        $path = $file->store('photos/' . class_basename($this) . '/' . $this->id, 'public');
        
        // Generate thumbnail
        $thumbnail = $this->createThumbnail($file, $path);
        
        return $this->photos()->create([
            'job_field_id' => $fieldId,
            'filename' => $file->getClientOriginalName(),
            'path' => $path,
            'thumbnail_path' => $thumbnail,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'metadata' => [
                'original_name' => $file->getClientOriginalName(),
                'extension' => $file->getClientOriginalExtension()
            ]
        ]);
    }

    public function removePhoto($photo)
    {
        Storage::disk('public')->delete($photo->path);
        if ($photo->thumbnail_path) {
            Storage::disk('public')->delete($photo->thumbnail_path);
        }
        return $photo->delete();
    }

    protected function createThumbnail($file, $path)
    {
        // This would use Intervention Image
        // For now, return null
        return null;
    }
}