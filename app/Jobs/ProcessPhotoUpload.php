<?php
// app/Jobs/ProcessPhotoUpload.php

namespace App\Jobs;

use App\Models\JobEntry;
use App\Services\PhotoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPhotoUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $jobEntry;
    protected $photos;
    protected $fieldId;

    public function __construct(JobEntry $jobEntry, array $photos, $fieldId = null)
    {
        $this->jobEntry = $jobEntry;
        $this->photos = $photos;
        $this->fieldId = $fieldId;
    }

    public function handle(PhotoService $photoService)
    {
        foreach ($this->photos as $photo) {
            $result = $photoService->upload($photo, "jobs/{$this->jobEntry->id}");
            
            $this->jobEntry->photos()->create([
                'job_field_id' => $this->fieldId,
                'filename' => $result['filename'],
                'path' => $result['path'],
                'thumbnail_path' => $result['thumbnail'],
                'mime_type' => $result['mime'],
                'size' => $result['size']
            ]);
        }
    }
}