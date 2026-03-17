<?php
// app/Http/Controllers/Api/DropdownValueController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DropdownValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DropdownValueController extends Controller
{
    public function index()
    {
        $values = DropdownValue::with('dropdownList')->get();
        
        return response()->json([
            'data' => $values
        ]);
    }

    public function show(DropdownValue $dropdownValue)
    {
        return response()->json([
            'data' => $dropdownValue->load('dropdownList')
        ]);
    }

    public function update(Request $request, DropdownValue $dropdownValue)
    {
        $validator = Validator::make($request->all(), [
            'value' => 'sometimes|required|string|max:255',
            'label' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'sort_order' => 'integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $dropdownValue->update($validator->validated());

        return response()->json([
            'message' => 'Dropdown value updated successfully',
            'data' => $dropdownValue
        ]);
    }

    public function destroy(DropdownValue $dropdownValue)
    {
        $dropdownValue->delete();
        
        return response()->json([
            'message' => 'Dropdown value deleted successfully'
        ]);
    }

    public function reorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:dropdown_values,id',
            'orders.*.order' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        foreach ($request->orders as $item) {
            DropdownValue::where('id', $item['id'])->update(['sort_order' => $item['order']]);
        }

        return response()->json([
            'message' => 'Values reordered successfully'
        ]);
    }
}