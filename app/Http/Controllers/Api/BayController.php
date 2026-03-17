<?php
// app/Http/Controllers/Api/BayController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bay;
use App\Models\Greenhouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BayController extends Controller
{
    public function index(Request $request)
    {
        $query = Bay::with('greenhouse');

        if ($request->filled('greenhouse_id')) {
            $query->where('greenhouse_id', $request->greenhouse_id);
        }

        $bays = $query->get();

        return response()->json([
            'data' => $bays
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'greenhouse_id' => 'required|exists:greenhouses,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'nullable|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $bay = Bay::create($validator->validated());

        return response()->json([
            'message' => 'Bay created successfully',
            'data' => $bay->load('greenhouse')
        ], 201);
    }

    public function show(Bay $bay)
    {
        return response()->json([
            'data' => $bay->load('greenhouse', 'jobEntries')
        ]);
    }

    public function update(Request $request, Bay $bay)
    {
        $validator = Validator::make($request->all(), [
            'greenhouse_id' => 'sometimes|required|exists:greenhouses,id',
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'nullable|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $bay->update($validator->validated());

        return response()->json([
            'message' => 'Bay updated successfully',
            'data' => $bay->fresh('greenhouse')
        ]);
    }

    public function destroy(Bay $bay)
    {
        // Check if bay has any job entries
        if ($bay->jobEntries()->exists()) {
            return response()->json([
                'message' => 'Cannot delete bay with existing job entries'
            ], 422);
        }

        $bay->delete();

        return response()->json([
            'message' => 'Bay deleted successfully'
        ]);
    }

    public function byGreenhouse(Greenhouse $greenhouse)
    {
        return response()->json([
            'data' => $greenhouse->bays
        ]);
    }
}