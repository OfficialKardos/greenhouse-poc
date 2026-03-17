<?php
// app/Http/Requests/UpdateBayRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBayRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->role === 'admin';
    }

    public function rules()
    {
        return [
            'greenhouse_id' => 'sometimes|required|exists:greenhouses,id',
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'nullable|integer|min:0'
        ];
    }
}