<?php
// app/Console/Commands/CleanupOldPhotos.php

namespace App\Console\Commands;

use App\Models\JobPhoto;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupOldPhotos extends Command
{
    protected $signature = 'photos:cleanup {days=30 : Delete photos older than X days}';
    protected $description = 'Delete old unused photos';

    public function handle()
    {
        $days = $this->argument('days');
        $cutoff = now()->subDays($days);

        $photos = JobPhoto::where('created_at', '<', $cutoff)->get();

        foreach ($photos as $photo) {
            Storage::disk('public')->delete($photo->path);
            if ($photo->thumbnail_path) {
                Storage::disk('public')->delete($photo->thumbnail_path);
            }
            $photo->delete();
            
            $this->info("Deleted photo: {$photo->filename}");
        }

        $this->info("Cleaned up {$photos->count()} photos");
    }
}