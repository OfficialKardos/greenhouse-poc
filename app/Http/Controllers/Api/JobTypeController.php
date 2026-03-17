<?php
// app/Http/Controllers/Api/JobTypeController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobType;
use App\Models\JobField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobTypeController extends Controller
{
    public function index()
    {
        $jobTypes = JobType::with('fields')->orderBy('sort_order')->get();

        return response()->json([
            'data' => $jobTypes
        ]);
    }

    public function show(JobType $jobType)
    {
        return response()->json([
            'data' => $jobType->load('fields.dropdownSource.values')
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'sort_order' => 'integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $jobType = JobType::create($validator->validated());

        return response()->json([
            'message' => 'Job type created successfully',
            'data' => $jobType
        ], 201);
    }

    public function update(Request $request, JobType $jobType)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'sort_order' => 'integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $jobType->update($validator->validated());

        return response()->json([
            'message' => 'Job type updated successfully',
            'data' => $jobType
        ]);
    }

    public function destroy(JobType $jobType)
    {
        $jobType->delete();

        return response()->json([
            'message' => 'Job type deleted successfully'
        ]);
    }

    public function fields(JobType $jobType)
    {
        // Only return active fields
        $fields = $jobType->fields()
            ->where('is_active', true)
            ->with('dropdownSource.activeValues')
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'data' => $fields
        ]);
    }
}