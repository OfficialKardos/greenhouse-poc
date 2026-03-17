<?php
// app/Http/Requests/UpdateDropdownValueRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDropdownValueRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->role === 'admin';
    }

    public function rules()
    {
        return [
            'value' => 'sometimes|required|string|max:255',
            'label' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'sort_order' => 'integer'
        ];
    }
}