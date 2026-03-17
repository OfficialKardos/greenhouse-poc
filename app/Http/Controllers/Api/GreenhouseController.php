<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Greenhouse;
use Illuminate\Http\Request;

class GreenhouseController extends Controller
{
    public function index()
    {
        $greenhouses = Greenhouse::with('bays')->get();

        return response()->json([
            'data' => $greenhouses
        ]);
    }

    public function show(Greenhouse $greenhouse)
    {
        return response()->json([
            'data' => $greenhouse->load('bays')
        ]);
    }

    public function bays(Greenhouse $greenhouse)
    {
        return response()->json([
            'data' => $greenhouse->bays
        ]);
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'address' => 'nullable|string',
        'city' => 'nullable|string',
        'state' => 'nullable|string',
        'zip' => 'nullable|string',
        'bays' => 'nullable|array',
        'bays.*.name' => 'required|string'
    ]);

    $greenhouse = Greenhouse::create($validated);

    if (!empty($validated['bays'])) {
        foreach ($validated['bays'] as $bay) {
            if (!empty($bay['name'])) {
                $greenhouse->bays()->create(['name' => $bay['name']]);
            }
        }
    }

    return response()->json([
        'message' => 'Greenhouse created successfully',
        'data' => $greenhouse->load('bays')
    ], 201);
}

public function update(Request $request, Greenhouse $greenhouse)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'address' => 'nullable|string',
        'city' => 'nullable|string',
        'state' => 'nullable|string',
        'zip' => 'nullable|string',
        'bays' => 'nullable|array',
        'bays.*.name' => 'required|string'
    ]);

    $greenhouse->update($validated);

    // Update bays
    if (isset($validated['bays'])) {
        $greenhouse->bays()->delete();
        foreach ($validated['bays'] as $bay) {
            if (!empty($bay['name'])) {
                $greenhouse->bays()->create(['name' => $bay['name']]);
            }
        }
    }

    return response()->json([
        'message' => 'Greenhouse updated successfully',
        'data' => $greenhouse->fresh('bays')
    ]);
}

public function destroy(Greenhouse $greenhouse)
{
    $greenhouse->delete();
    
    return response()->json([
        'message' => 'Greenhouse deleted successfully'
    ]);
}
}