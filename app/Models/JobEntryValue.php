<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobEntryValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_entry_id',
        'job_field_id',
        'value'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function jobEntry()
    {
        return $this->belongsTo(JobEntry::class);
    }

    public function jobField()
    {
        return $this->belongsTo(JobField::class);
    }
}