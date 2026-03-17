<?php
// app/Http/Requests/StoreJobFieldRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobFieldRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->role === 'admin';
    }

    public function rules()
    {
        return [
            'label' => 'required|string|max:255',
            'field_name' => 'required|string|max:255|unique:job_fields,field_name,NULL,id,job_type_id,' . $this->job_type_id,
            'field_type' => 'required|in:text,textarea,number,dropdown,yes_no,temperature,photo,date,ppm,ph,ec,time,datetime,signature',
            'is_required' => 'boolean',
            'dropdown_source_id' => 'nullable|exists:dropdown_lists,id',
            'placeholder' => 'nullable|string',
            'help_text' => 'nullable|string',
            'validation_rules' => 'nullable|array',
            'settings' => 'nullable|array',
            'sort_order' => 'integer'
        ];
    }
}