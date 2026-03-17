<?php
// app/Http/Requests/UpdateDropdownListRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDropdownListRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->role === 'admin';
    }

    public function rules()
    {
        return [
            'name' => 'sometimes|required|string|max:255|unique:dropdown_lists,name,' . $this->dropdown_list->id,
            'description' => 'nullable|string'
        ];
    }
}