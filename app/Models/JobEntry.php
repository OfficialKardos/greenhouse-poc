<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_number',
        'user_id',
        'greenhouse_id',
        'bay_id',
        'job_type_id',
        'status',
        'notes',
        'metadata',
        'submitted_at',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'metadata' => 'array',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($entry) {
            if (!$entry->job_number) {
                $entry->job_number = 'JOB-' . date('Ymd') . '-' . str_pad(static::max('id') + 1, 4, '0', STR_PAD_LEFT);
            }
            if (!$entry->submitted_at) {
                $entry->submitted_at = now();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function greenhouse()
    {
        return $this->belongsTo(Greenhouse::class);
    }

    public function bay()
    {
        return $this->belongsTo(Bay::class);
    }

    public function jobType()
    {
        return $this->belongsTo(JobType::class);
    }

    public function values()
    {
        return $this->hasMany(JobEntryValue::class);
    }

    public function photos()
    {
        return $this->hasMany(JobPhoto::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getFieldValue($fieldName)
    {
        $field = JobField::where('job_type_id', $this->job_type_id)
            ->where('field_name', $fieldName)
            ->first();

        if ($field) {
            $value = $this->values()->where('job_field_id', $field->id)->first();
            return $value ? $value->value : null;
        }

        return null;
    }
}