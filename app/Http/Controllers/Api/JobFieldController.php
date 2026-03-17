<?php
// app/Http/Controllers/Api/JobFieldController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobField;
use App\Models\JobType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobFieldController extends Controller
{
    public function index(JobType $jobType)
    {
        // Show ALL fields in admin (including inactive)
        $fields = $jobType->fields()
            ->with('dropdownSource')
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'data' => $fields
        ]);
    }

    public function store(Request $request, JobType $jobType)
    {
        $validator = Validator::make($request->all(), [
            'label' => 'required|string|max:255',
            'field_name' => 'required|string|max:255|unique:job_fields,field_name,NULL,id,job_type_id,' . $jobType->id,
            'field_type' => 'required|in:text,textarea,number,dropdown,yes_no,temperature,photo,date,ppm,ph,ec,time,datetime,signature',
            'is_required' => 'boolean',
            'dropdown_source_id' => 'nullable|exists:dropdown_lists,id',
            'placeholder' => 'nullable|string',
            'help_text' => 'nullable|string',
            'validation_rules' => 'nullable|array',
            'settings' => 'nullable|array',
            'sort_order' => 'integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $maxOrder = JobField::where('job_type_id', $jobType->id)->max('sort_order') ?? 0;

        $field = $jobType->fields()->create(array_merge(
            $validator->validated(),
            ['sort_order' => $request->sort_order ?? $maxOrder + 1]
        ));

        return response()->json([
            'message' => 'Job field created successfully',
            'data' => $field->load('dropdownSource')
        ], 201);
    }

    public function show(JobField $jobField)
    {
        return response()->json([
            'data' => $jobField->load('dropdownSource', 'jobType')
        ]);
    }

    public function update(Request $request, JobField $jobField)
    {
        $validator = Validator::make($request->all(), [
            'label' => 'sometimes|required|string|max:255',
            'field_name' => 'sometimes|required|string|max:255|unique:job_fields,field_name,' . $jobField->id . ',id,job_type_id,' . $jobField->job_type_id,
            'field_type' => 'sometimes|required|in:text,textarea,number,dropdown,yes_no,temperature,photo,date,ppm,ph,ec,time,datetime,signature',
            'is_required' => 'boolean',
            'dropdown_source_id' => 'nullable|exists:dropdown_lists,id',
            'placeholder' => 'nullable|string',
            'help_text' => 'nullable|string',
            'is_active' => 'boolean', // Make sure this is included
            'sort_order' => 'integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $jobField->update($validator->validated());

        return response()->json([
            'message' => 'Job field updated successfully',
            'data' => $jobField->fresh('dropdownSource')
        ]);
    }

    public function destroy(JobField $jobField)
    {
        try {
            // Check if there are any job entry values using this field
            $entriesCount = $jobField->entryValues()->count();

            if ($entriesCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete this field because it has {$entriesCount} job entries associated with it. Archive it instead."
                ], 422);
            }

            // Safe to delete
            $jobField->delete();

            return response()->json([
                'success' => true,
                'message' => 'Job field deleted successfully'
            ]);

        } catch (\Illuminate\Database\QueryException $e) {
            // If there's a foreign key constraint, return a friendly message
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete this field because it is being used by existing job entries.'
            ], 422);
        }
    }

    public function reorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:job_fields,id',
            'orders.*.order' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        foreach ($request->orders as $item) {
            JobField::where('id', $item['id'])->update(['sort_order' => $item['order']]);
        }

        return response()->json([
            'message' => 'Fields reordered successfully'
        ]);
    }
}