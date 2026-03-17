<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobEntry;
use App\Models\JobField;
use App\Models\JobPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class JobEntryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'greenhouse_id' => 'required|exists:greenhouses,id',
            'bay_id' => 'required|exists:bays,id',
            'job_type_id' => 'required|exists:job_types,id',
            'values' => 'required|array',
            'notes' => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {
            $jobEntry = JobEntry::create([
                'user_id' => $request->user()->id,
                'greenhouse_id' => $request->greenhouse_id,
                'bay_id' => $request->bay_id,
                'job_type_id' => $request->job_type_id,
                'notes' => $request->notes,
                'submitted_at' => now()
            ]);

            // Save field values
            foreach ($request->values as $fieldId => $value) {
                $jobEntry->values()->create([
                    'job_field_id' => $fieldId,
                    'value' => $value
                ]);
            }

            // Handle photo uploads
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $fieldId => $file) {
                    $this->savePhoto($jobEntry, $fieldId, $file);
                }
            } elseif ($request->has('photos') && is_array($request->photos)) {
                // Handle base64 or temp paths from offline mode
                foreach ($request->photos as $fieldId => $photoData) {
                    $this->processPhotoData($jobEntry, $fieldId, $photoData);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Job created successfully',
                'data' => $jobEntry->load('values', 'photos')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Job creation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create job',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    protected function savePhoto($jobEntry, $fieldId, $file)
    {
        try {
            // Generate unique filename
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Store in jobs directory
            $path = $file->storeAs('jobs/' . $jobEntry->id, $filename, 'public');

            // Save to database
            $jobEntry->photos()->create([
                'job_field_id' => $fieldId,
                'filename' => $filename,
                'path' => $path,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize()
            ]);

        } catch (\Exception $e) {
            Log::error('Photo save failed: ' . $e->getMessage());
        }
    }

    protected function processPhotoData($jobEntry, $fieldId, $photoData)
    {
        // If it's a temp path from storage
        if (is_string($photoData) && Storage::disk('public')->exists($photoData)) {
            $this->moveTempPhoto($jobEntry, $fieldId, $photoData);
        }
    }

    protected function moveTempPhoto($jobEntry, $fieldId, $tempPath)
    {
        try {
            $filename = basename($tempPath);
            $newPath = 'jobs/' . $jobEntry->id . '/' . $filename;

            if (Storage::disk('public')->move($tempPath, $newPath)) {
                $jobEntry->photos()->create([
                    'job_field_id' => $fieldId,
                    'filename' => $filename,
                    'path' => $newPath,
                    'mime_type' => Storage::disk('public')->mimeType($newPath),
                    'size' => Storage::disk('public')->size($newPath)
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Temp photo move failed: ' . $e->getMessage());
        }
    }

    public function index(Request $request)
    {
        $query = JobEntry::with(['user', 'greenhouse', 'bay', 'jobType', 'values.jobField', 'photos']);

        // Apply filters
        if ($request->has('greenhouse_id')) {
            $query->where('greenhouse_id', $request->greenhouse_id);
        }

        if ($request->has('bay_id')) {
            $query->where('bay_id', $request->bay_id);
        }

        if ($request->has('job_type_id')) {
            $query->where('job_type_id', $request->job_type_id);
        }

        if ($request->has('from_date')) {
            $query->whereDate('submitted_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('submitted_at', '<=', $request->to_date);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $entries = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json($entries);
    }

    public function show(JobEntry $jobEntry)
    {
        return response()->json([
            'data' => $jobEntry->load(['user', 'greenhouse', 'bay', 'jobType', 'values.jobField', 'photos'])
        ]);
    }
}