<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DropdownList extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function values()
    {
        return $this->hasMany(DropdownValue::class)->orderBy('sort_order');
    }

    public function activeValues()
    {
        return $this->hasMany(DropdownValue::class)->where('is_active', true)->orderBy('sort_order');
    }

    public function jobFields()
    {
        return $this->hasMany(JobField::class, 'dropdown_source_id');
    }
}