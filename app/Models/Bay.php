<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bay extends Model
{
    use HasFactory;

    protected $fillable = [
        'greenhouse_id',
        'name',
        'description',
        'capacity'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function greenhouse()
    {
        return $this->belongsTo(Greenhouse::class);
    }

    public function jobEntries()
    {
        return $this->hasMany(JobEntry::class);
    }
}