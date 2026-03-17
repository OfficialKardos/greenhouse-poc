<?php
// app/Http/Requests/UpdateJobFieldRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJobFieldRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->role === 'admin';
    }

    public function rules()
    {
        return [
            'label' => 'sometimes|required|string|max:255',
            'field_name' => 'sometimes|required|string|max:255|unique:job_fields,field_name,' . $this->job_field->id . ',id,job_type_id,' . $this->job_field->job_type_id,
            'field_type' => 'sometimes|required|in:text,textarea,number,dropdown,yes_no,temperature,photo,date,ppm,ph,ec,time,datetime,signature',
            'is_required' => 'boolean',
            'dropdown_source_id' => 'nullable|exists:dropdown_lists,id',
            'placeholder' => 'nullable|string',
            'help_text' => 'nullable|string',
            'validation_rules' => 'nullable|array',
            'settings' => 'nullable|array',
            'is_active' => 'boolean',
            'sort_order' => 'integer'
        ];
    }
}