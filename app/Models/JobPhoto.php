<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class JobPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_entry_id',
        'job_field_id',
        'filename',
        'path',
        'thumbnail_path',
        'mime_type',
        'size',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'size' => 'integer'
    ];

    protected $appends = ['url', 'thumbnail_url'];

    public function jobEntry()
    {
        return $this->belongsTo(JobEntry::class);
    }

    public function jobField()
    {
        return $this->belongsTo(JobField::class);
    }

    public function getUrlAttribute()
    {
        return $this->path ? Storage::disk('public')->url($this->path) : null;
    }

    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail_path ? Storage::disk('public')->url($this->thumbnail_path) : $this->url;
    }
}