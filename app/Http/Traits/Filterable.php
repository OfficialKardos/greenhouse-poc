<?php
// app/Traits/Filterable.php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    public function scopeApplyFilters(Builder $query, array $filters)
    {
        foreach ($filters as $key => $value) {
            if (method_exists($this, 'scopeFilterBy' . ucfirst($key))) {
                $query->{'filterBy' . ucfirst($key)}($value);
            } elseif ($this->isFilterable($key)) {
                $query->where($key, $value);
            }
        }
        
        return $query;
    }

    protected function isFilterable($column)
    {
        return in_array($column, $this->filterable ?? []);
    }
}