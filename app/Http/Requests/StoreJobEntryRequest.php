<?php
// app/Http/Requests/StoreJobEntryRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobEntryRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Workers can submit
    }

    public function rules()
    {
        return [
            'greenhouse_id' => 'required|exists:greenhouses,id',
            'bay_id' => 'required|exists:bays,id',
            'job_type_id' => 'required|exists:job_types,id',
            'values' => 'required|array',
            'values.*' => 'required',
            'photos' => 'nullable|array',
            'photos.*' => 'nullable|image|max:10120', // 5MB max
            'notes' => 'nullable|string'
        ];
    }

    public function messages()
    {
        return [
            'photos.*.max' => 'Each photo must not exceed 10MB',
            'photos.*.image' => 'File must be an image'
        ];
    }
}