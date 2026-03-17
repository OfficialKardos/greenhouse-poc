<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Services\MobileApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use App\Models\JobEntry;
use App\Models\JobField;

class WorkerController extends Controller
{
    protected $apiService;

    public function __construct(MobileApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function showLogin()
    {
        return view('mobile.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        Log::info('Mobile login attempt', ['email' => $request->email]);

        $result = $this->apiService->login($request->email, $request->password);

        if ($result['success']) {
            session([
                'worker_token' => $result['token'],
                'worker_user' => $result['user']
            ]);

            Log::info('Login successful', ['user' => $result['user']['email']]);
            return redirect('/mobile/dashboard');
        }

        return back()->with('error', 'Invalid credentials')->withInput();
    }

    public function dashboard()
    {
        $this->checkAuth();

        $result = $this->apiService->getGreenhouses();

        return view('mobile.greenhouses', [
            'greenhouses' => $result['data']
        ]);
    }

    public function jobs($greenhouseId)
    {
        $this->checkAuth();

        $result = $this->apiService->getJobTypes();

        return view('mobile.jobs', [
            'jobTypes' => $result['data'],
            'greenhouseId' => $greenhouseId
        ]);
    }

    public function bays($greenhouseId, $jobTypeId)
    {
        $this->checkAuth();

        $result = $this->apiService->getBays($greenhouseId);

        return view('mobile.bays', [
            'bays' => $result['data'],
            'greenhouseId' => $greenhouseId,
            'jobTypeId' => $jobTypeId
        ]);
    }

    public function form($greenhouseId, $bayId, $jobTypeId)
    {
        $this->checkAuth();

        $result = $this->apiService->getJobFields($jobTypeId);

        return view('mobile.form', [
            'fields' => $result['data'],
            'greenhouseId' => $greenhouseId,
            'bayId' => $bayId,
            'jobTypeId' => $jobTypeId
        ]);
    }

    public function submit(Request $request)
    {
        $this->checkAuth();

        $request->validate([
            'greenhouse_id' => 'required',
            'bay_id' => 'required',
            'job_type_id' => 'required',
            'values' => 'required|array',
        ]);

        $data = $request->all();

        // Handle file uploads
        if ($request->hasFile('photos')) {
            $photos = [];
            foreach ($request->file('photos') as $fieldId => $file) {
                $path = $file->store('temp', 'public');
                $photos[$fieldId] = $path;
            }
            $data['photos'] = $photos;
        }

        $user = session('worker_user');
        $result = $this->apiService->createJobEntry($data, $user['id']);

        if ($result['success']) {
            // Clean up temp files
            if (isset($data['photos'])) {
                foreach ($data['photos'] as $path) {
                    Storage::disk('public')->delete($path);
                }
            }

            return redirect('/mobile/dashboard')->with('success', 'Job submitted successfully!');
        }

        return redirect('/mobile/dashboard')->with('error', 'Failed to submit job');
    }

    public function logout()
    {
        session()->forget(['worker_token', 'worker_user']);
        return redirect('/mobile/login');
    }

    protected function checkAuth()
    {
        if (!session('worker_token')) {
            redirect('/mobile/login')->send();
            exit;
        }
    }

    public function submitted(Request $request)
    {
        $this->checkAuth();

        $user = session('worker_user');

        // Get job types for filter
        $jobTypes = Cache::get('job_types', []);

        // Get user's jobs with filters
        $jobs = $this->getUserJobs($user['id'], $request);

        return view('mobile.submitted', [
            'jobs' => $jobs,
            'jobTypes' => $jobTypes
        ]);
    }

    public function filterSubmitted(Request $request)
    {
        $this->checkAuth();

        $user = session('worker_user');
        $jobs = $this->getUserJobs($user['id'], $request);

        return view('mobile.partials.jobs-list', ['jobs' => $jobs]);
    }

    protected function getUserJobs($userId, $request)
    {
        try {
            $query = JobEntry::with(['greenhouse', 'bay', 'jobType'])
                ->where('user_id', $userId);

            // Apply date filters
            if ($request->filled('from_date')) {
                $query->whereDate('submitted_at', '>=', $request->from_date);
            }

            if ($request->filled('to_date')) {
                $query->whereDate('submitted_at', '<=', $request->to_date);
            }

            // Apply job type filter
            if ($request->filled('job_type')) {
                $query->where('job_type_id', $request->job_type);
            }

            // Apply status filter
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Get with photos count
            $jobs = $query->withCount('photos')
                ->orderBy('submitted_at', 'desc')
                ->get();

            return $jobs;

        } catch (\Exception $e) {
            Log::error('Failed to get user jobs: ' . $e->getMessage());
            return collect();
        }
    }

    public function edit($id)
    {
        $this->checkAuth();

        $user = session('worker_user');

        try {
            // Always get a fresh copy from the database
            $job = JobEntry::with(['values', 'photos'])
                ->where('id', $id)
                ->where('user_id', $user['id'])
                ->first();

            if (!$job) {
                return response()->json(['success' => false, 'message' => 'Job not found'], 404);
            }

            // Force refresh from database to ensure latest data
            $job->refresh();

            // Get job fields for this job type with their current values
            $fields = JobField::where('job_type_id', $job->job_type_id)
                ->with('dropdownSource.values')
                ->orderBy('sort_order')
                ->get()
                ->map(function ($field) use ($job) {
                    // Make sure we're getting the absolute latest value
                    $currentValue = $job->values()
                        ->where('job_field_id', $field->id)
                        ->first();

                    $field->current_value = $currentValue ? $currentValue->value : null;
                    return $field;
                });

            // Log for debugging
            Log::info('Edit form loaded', [
                'job_id' => $id,
                'field_count' => $fields->count(),
                'values' => $fields->pluck('current_value', 'id')
            ]);

            return view('mobile.partials.edit-form', [
                'job' => $job,
                'fields' => $fields
            ]);

        } catch (\Exception $e) {
            Log::error('Edit error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to load edit form'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        // Debug logging
        Log::info('=== UPDATE REQUEST RECEIVED ===', [
            'id' => $id,
            'method' => $request->method(),
            'all' => $request->all(),
            'values' => $request->input('values', []),
            'has_files' => $request->hasFile('new_photos')
        ]);

        $this->checkAuth();
        $user = session('worker_user');

        try {
            $job = JobEntry::where('id', $id)
                ->where('user_id', $user['id'])
                ->first();

            if (!$job) {
                Log::error('Job not found', ['id' => $id, 'user_id' => $user['id']]);
                return redirect('/mobile/submitted')->with('error', 'Job not found');
            }

            // Only allow editing if status is 'submitted'
            if ($job->status !== 'submitted') {
                return redirect('/mobile/submitted')->with('error', 'Only submitted jobs can be edited');
            }

            DB::beginTransaction();

            // Update field values
            if ($request->has('values')) {
                Log::info('Processing values', ['count' => count($request->values)]);

                foreach ($request->values as $fieldId => $value) {
                    $jobValue = $job->values()->where('job_field_id', $fieldId)->first();

                    if ($jobValue) {
                        $jobValue->value = $value;
                        $jobValue->save();
                        Log::info('Updated field', ['field_id' => $fieldId, 'value' => $value]);
                    } else {
                        $job->values()->create([
                            'job_field_id' => $fieldId,
                            'value' => $value
                        ]);
                        Log::info('Created new field', ['field_id' => $fieldId, 'value' => $value]);
                    }
                }
            }

            // Update notes
            if ($request->has('notes')) {
                $job->notes = $request->notes;
                $job->save();
            }

            // Handle photo deletions
            if ($request->has('deleted_photos')) {
                $deletedPhotos = json_decode($request->deleted_photos, true);
                if (is_array($deletedPhotos)) {
                    foreach ($deletedPhotos as $photoId) {
                        $photo = $job->photos()->find($photoId);
                        if ($photo) {
                            Storage::disk('public')->delete($photo->path);
                            $photo->delete();
                        }
                    }
                }
            }

            // Handle new photo uploads
            if ($request->hasFile('new_photos')) {
                foreach ($request->file('new_photos') as $file) {
                    $validator = Validator::make(
                        ['photo' => $file],
                        ['photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120']
                    );

                    if ($validator->fails()) {
                        continue;
                    }

                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('jobs/' . $job->id, $filename, 'public');

                    $job->photos()->create([
                        'job_field_id' => null,
                        'filename' => $filename,
                        'path' => $path,
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize()
                    ]);
                }
            }

            DB::commit();

            Log::info('Update successful', ['job_id' => $id]);

            // Redirect using direct URL instead of named route
            return redirect("/mobile/jobs/{$id}/view")
                ->with('success', 'Job updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update error: ' . $e->getMessage());
            return redirect("/mobile/submitted")
                ->with('error', 'Failed to update job: ' . $e->getMessage());
        }
    }

    public function view($id)
    {
        $this->checkAuth();

        $user = session('worker_user');

        try {
            $job = JobEntry::with([
                'greenhouse',
                'bay',
                'jobType',
                'values.jobField',
                'photos'
            ])
                ->where('id', $id)
                ->where('user_id', $user['id'])
                ->firstOrFail();

            return view('mobile.job-view', ['job' => $job]);

        } catch (\Exception $e) {
            Log::error('Job view error: ' . $e->getMessage());
            return redirect('/mobile/submitted')
                ->with('error', 'Job not found');
        }
    }
}