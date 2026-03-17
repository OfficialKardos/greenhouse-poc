<?php
// app/Http/Requests/StoreDropdownListRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDropdownListRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->role === 'admin';
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:dropdown_lists',
            'description' => 'nullable|string'
        ];
    }
}