<?php

namespace App\Services;

use App\Models\User;
use App\Models\Greenhouse;
use App\Models\JobType;
use App\Models\Bay;
use App\Models\JobField;
use App\Models\JobEntry;
use App\Models\JobPhoto;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MobileApiService
{
    public function login($email, $password)
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return [
                'success' => false,
                'message' => 'Invalid credentials'
            ];
        }

        // Create token
        $token = $user->createToken('mobile_' . uniqid())->plainTextToken;

        return [
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ]
        ];
    }

    public function getGreenhouses()
    {
        $greenhouses = Greenhouse::with('bays')->get();

        return [
            'success' => true,
            'data' => $greenhouses
        ];
    }

    public function getJobTypes()
    {
        $jobTypes = JobType::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return [
            'success' => true,
            'data' => $jobTypes
        ];
    }

    public function getBays($greenhouseId)
    {
        $bays = Bay::where('greenhouse_id', $greenhouseId)->get();

        return [
            'success' => true,
            'data' => $bays
        ];
    }

    public function getJobFields($jobTypeId)
    {
        $fields = JobField::where('job_type_id', $jobTypeId)
            ->where('is_active', true)  // Only active fields
            ->with('dropdownSource.values')
            ->orderBy('sort_order')
            ->get();

        return [
            'success' => true,
            'data' => $fields
        ];
    }

    public function createJobEntry($data, $userId)
    {
        try {
            DB::beginTransaction();

            $jobEntry = JobEntry::create([
                'user_id' => $userId,
                'greenhouse_id' => $data['greenhouse_id'],
                'bay_id' => $data['bay_id'],
                'job_type_id' => $data['job_type_id'],
                'notes' => $data['notes'] ?? null,
                'submitted_at' => now(),
                'status' => 'submitted'
            ]);

            // Save field values
            foreach ($data['values'] as $fieldId => $value) {
                $jobEntry->values()->create([
                    'job_field_id' => $fieldId,
                    'value' => $value
                ]);
            }

            // Handle photos if present
            if (isset($data['photos']) && is_array($data['photos'])) {
                foreach ($data['photos'] as $fieldId => $photoPath) {
                    if (is_string($photoPath) && file_exists(storage_path('app/public/' . $photoPath))) {
                        // Move from temp to permanent location
                        $filename = basename($photoPath);
                        $newPath = 'jobs/' . $jobEntry->id . '/' . $filename;

                        Storage::disk('public')->move($photoPath, $newPath);

                        $jobEntry->photos()->create([
                            'job_field_id' => $fieldId,
                            'filename' => $filename,
                            'path' => $newPath,
                            'mime_type' => Storage::disk('public')->mimeType($newPath),
                            'size' => Storage::disk('public')->size($newPath)
                        ]);
                    }
                }
            }

            DB::commit();

            return [
                'success' => true,
                'data' => $jobEntry
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Job creation failed: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to create job',
                'error' => $e->getMessage()
            ];
        }
    }
}