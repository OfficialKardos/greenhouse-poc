<?php
// app/Http/Controllers/Api/DropdownListController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DropdownList;
use App\Models\DropdownValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DropdownListController extends Controller
{
    public function index()
    {
        $dropdowns = DropdownList::withCount('values')->get();
        
        return response()->json([
            'data' => $dropdowns
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:dropdown_lists',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $dropdown = DropdownList::create($validator->validated());

        return response()->json([
            'message' => 'Dropdown list created successfully',
            'data' => $dropdown
        ], 201);
    }

    public function show(DropdownList $dropdownList)
    {
        return response()->json([
            'data' => $dropdownList->load('values')
        ]);
    }

    public function update(Request $request, DropdownList $dropdownList)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255|unique:dropdown_lists,name,' . $dropdownList->id,
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $dropdownList->update($validator->validated());

        return response()->json([
            'message' => 'Dropdown list updated successfully',
            'data' => $dropdownList
        ]);
    }

    public function destroy(DropdownList $dropdownList)
    {
        // Check if being used by any job fields
        if ($dropdownList->jobFields()->exists()) {
            return response()->json([
                'message' => 'Cannot delete dropdown list that is in use by job fields'
            ], 422);
        }

        $dropdownList->delete();
        
        return response()->json([
            'message' => 'Dropdown list deleted successfully'
        ]);
    }

    public function values(DropdownList $dropdownList)
    {
        return response()->json([
            'data' => $dropdownList->values()->orderBy('sort_order')->get()
        ]);
    }

    public function storeValue(Request $request, DropdownList $dropdownList)
    {
        $validator = Validator::make($request->all(), [
            'value' => 'required|string|max:255',
            'label' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:50',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $maxOrder = $dropdownList->values()->max('sort_order') ?? 0;
        
        $value = $dropdownList->values()->create(array_merge(
            $validator->validated(),
            ['sort_order' => $maxOrder + 1]
        ));

        return response()->json([
            'message' => 'Dropdown value created successfully',
            'data' => $value
        ], 201);
    }
}