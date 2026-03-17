<?php
// app/Http/Resources/DropdownValueResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DropdownValueResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'dropdown_list_id' => $this->dropdown_list_id,
            'value' => $this->value,
            'label' => $this->label,
            'display_label' => $this->display_label,
            'color' => $this->color,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}