<?php
// app/Services/PhotoService.php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class PhotoService
{
    protected $disk = 'public';
    protected $maxSize = 10120; // 5MB
    protected $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    public function upload(UploadedFile $file, string $path, array $options = [])
    {
        $this->validateFile($file);

        $filename = $this->generateFilename($file);
        $fullPath = $path . '/' . $filename;

        // Store original
        Storage::disk($this->disk)->putFileAs($path, $file, $filename);

        // Create thumbnail if needed
        $thumbnail = null;
        if ($options['thumbnail'] ?? true) {
            $thumbnail = $this->createThumbnail($file, $path, $filename);
        }

        return [
            'path' => $fullPath,
            'thumbnail' => $thumbnail,
            'filename' => $filename,
            'size' => $file->getSize(),
            'mime' => $file->getMimeType()
        ];
    }

    public function delete(string $path)
    {
        if (Storage::disk($this->disk)->exists($path)) {
            return Storage::disk($this->disk)->delete($path);
        }
        return false;
    }

    protected function validateFile(UploadedFile $file)
    {
        if (!in_array($file->getMimeType(), $this->allowedTypes)) {
            throw new \Exception('Invalid file type');
        }

        if ($file->getSize() > $this->maxSize * 1024) {
            throw new \Exception('File too large');
        }
    }

    protected function generateFilename(UploadedFile $file)
    {
        return time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
    }

    protected function createThumbnail(UploadedFile $file, string $path, string $filename)
    {
        // This would use Intervention Image
        // For now, return original path
        return $path . '/thumb_' . $filename;
    }
}