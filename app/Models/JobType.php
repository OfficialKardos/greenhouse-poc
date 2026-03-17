<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'icon',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function fields()
    {
        return $this->hasMany(JobField::class)->orderBy('sort_order');
    }

    public function activeFields()
    {
        return $this->hasMany(JobField::class)->where('is_active', true)->orderBy('sort_order');
    }

    public function jobEntries()
    {
        return $this->hasMany(JobEntry::class);
    }
}