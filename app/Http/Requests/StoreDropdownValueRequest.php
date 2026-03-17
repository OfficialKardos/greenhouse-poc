<?php
// app/Http/Requests/StoreDropdownValueRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDropdownValueRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->role === 'admin';
    }

    public function rules()
    {
        return [
            'value' => 'required|string|max:255',
            'label' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:50',
            'is_active' => 'boolean'
        ];
    }
}