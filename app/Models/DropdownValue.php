<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DropdownValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'dropdown_list_id',
        'value',
        'label',
        'color',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function dropdownList()
    {
        return $this->belongsTo(DropdownList::class);
    }

    public function getDisplayLabelAttribute()
    {
        return $this->label ?? $this->value;
    }
}