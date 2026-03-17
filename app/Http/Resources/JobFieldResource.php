<?php
// app/Http/Resources/JobFieldResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JobFieldResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'job_type_id' => $this->job_type_id,
            'label' => $this->label,
            'field_name' => $this->field_name,
            'field_type' => $this->field_type,
            'placeholder' => $this->placeholder,
            'help_text' => $this->help_text,
            'is_required' => $this->is_required,
            'dropdown_source_id' => $this->dropdown_source_id,
            'dropdown_source' => new DropdownListResource($this->whenLoaded('dropdownSource')),
            'dropdown_values' => $this->when($this->dropdownSource, function() {
                return $this->dropdownSource->activeValues;
            }),
            'validation_rules' => $this->validation_rules,
            'settings' => $this->settings,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}