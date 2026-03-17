<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobField extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'job_type_id',
        'label',
        'field_name',
        'field_type',
        'placeholder',
        'help_text',
        'is_required',
        'dropdown_source_id',
        'validation_rules',
        'settings',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'validation_rules' => 'array',
        'settings' => 'array',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function jobType()
    {
        return $this->belongsTo(JobType::class);
    }

    public function dropdownSource()
    {
        return $this->belongsTo(DropdownList::class, 'dropdown_source_id');
    }

    public function entryValues()
    {
        return $this->hasMany(JobEntryValue::class);
    }

    public function photos()
    {
        return $this->hasMany(JobPhoto::class);
    }

    public function getDropdownValuesAttribute()
    {
        if ($this->dropdownSource) {
            return $this->dropdownSource->activeValues;
        }
        return collect();
    }
}