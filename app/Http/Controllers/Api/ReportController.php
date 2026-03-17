<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobEntry;
use App\Models\JobType;
use App\Models\Greenhouse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    /**
     * Get summary report of all job entries
     */
    public function summary(Request $request)
    {
        try {
            $query = JobEntry::with([
                'user',
                'greenhouse',
                'bay',
                'jobType',
                'values.jobField',
                'photos'
            ]);

            $this->applyFilters($query, $request);

            $entries = $query->latest()->get();

            // Calculate summary statistics
            $totalEntries = $entries->count();
            $withPhotos = $entries->filter(fn($e) => $e->photos->count() > 0)->count();
            $uniqueWorkers = $entries->pluck('user_id')->unique()->count();

            // Group by job type
            $byJobType = [];
            foreach ($entries as $entry) {
                $type = $entry->jobType?->name ?? 'Unknown';
                if (!isset($byJobType[$type])) {
                    $byJobType[$type] = 0;
                }
                $byJobType[$type]++;
            }

            // Group by status
            $byStatus = [
                'submitted' => $entries->where('status', 'submitted')->count(),
                'approved' => $entries->where('status', 'approved')->count(),
                'rejected' => $entries->where('status', 'rejected')->count(),
                'draft' => $entries->where('status', 'draft')->count(),
            ];

            // Get date range
            $dates = $entries->pluck('submitted_at')->filter();
            $dateRange = [
                'from' => $request->from_date ?? ($dates->min()?->format('Y-m-d')),
                'to' => $request->to_date ?? ($dates->max()?->format('Y-m-d')),
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'entries' => $entries,
                    'summary' => [
                        'total_entries' => $totalEntries,
                        'with_photos' => $withPhotos,
                        'unique_workers' => $uniqueWorkers,
                        'by_job_type' => $byJobType,
                        'by_status' => $byStatus,
                        'date_range' => $dateRange,
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Report summary error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Chemical usage report
     */
    public function chemicals(Request $request)
    {
        try {
            // Get the Chemicals job type
            $chemicalsJobType = JobType::where('name', 'LIKE', '%chemical%')->first();

            if (!$chemicalsJobType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chemicals job type not found'
                ], 404);
            }

            $query = JobEntry::with([
                'user',
                'greenhouse',
                'bay',
                'values.jobField',
                'photos'
            ])->where('job_type_id', $chemicalsJobType->id);

            $this->applyFilters($query, $request);

            $entries = $query->latest()->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'entries' => $entries,  // Make sure entries are passed
                    'summary' => [           // Optional summary
                        'total_applications' => $entries->count(),
                        'total_volume' => 0,  // Will be calculated in frontend
                        'unique_chemicals' => 0,
                        'unique_plants' => 0,
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Report chemicals error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate chemicals report',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Soil samples report (pH and EC trends)
     */
    public function soil(Request $request)
    {
        try {
            // Get the Soil Samples job type
            $soilJobType = JobType::where('name', 'LIKE', '%soil%')->first();

            if (!$soilJobType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Soil Samples job type not found'
                ], 404);
            }

            $query = JobEntry::with([
                'greenhouse',
                'bay',
                'values.jobField',
                'photos'
            ])->where('job_type_id', $soilJobType->id);

            $this->applyFilters($query, $request);

            $entries = $query->orderBy('submitted_at')->get();

            // Process soil data by plant
            $trends = [];
            $allPlants = [];

            foreach ($entries as $entry) {
                $plant = $entry->getFieldValue('plant') ?? 'Unspecified';
                $week = $entry->getFieldValue('week');
                $ph = $entry->getFieldValue('ph');
                $ec = $entry->getFieldValue('ec');

                if (!in_array($plant, $allPlants)) {
                    $allPlants[] = $plant;
                }

                if (!isset($trends[$plant])) {
                    $trends[$plant] = [];
                }

                $trends[$plant][] = [
                    'id' => $entry->id,
                    'date' => $entry->submitted_at->format('Y-m-d'),
                    'week' => $week,
                    'ph' => $ph ? floatval($ph) : null,
                    'ec' => $ec ? floatval($ec) : null,
                    'greenhouse' => $entry->greenhouse?->name ?? 'Unknown',
                    'bay' => $entry->bay?->name ?? 'Unknown',
                    'photos' => $entry->photos,
                    'has_photos' => $entry->photos->count() > 0,
                ];
            }

            // Calculate statistics for each plant
            $statistics = [];
            foreach ($trends as $plant => $samples) {
                $phValues = array_filter(array_column($samples, 'ph'));
                $ecValues = array_filter(array_column($samples, 'ec'));

                $statistics[$plant] = [
                    'sample_count' => count($samples),
                    'avg_ph' => count($phValues) > 0 ? array_sum($phValues) / count($phValues) : null,
                    'avg_ec' => count($ecValues) > 0 ? array_sum($ecValues) / count($ecValues) : null,
                    'min_ph' => count($phValues) > 0 ? min($phValues) : null,
                    'max_ph' => count($phValues) > 0 ? max($phValues) : null,
                    'min_ec' => count($ecValues) > 0 ? min($ecValues) : null,
                    'max_ec' => count($ecValues) > 0 ? max($ecValues) : null,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'entries' => $entries,
                    'trends' => $trends,
                    'plants' => $allPlants,
                    'statistics' => $statistics,
                    'summary' => [
                        'total_samples' => $entries->count(),
                        'unique_plants' => count($allPlants),
                        'with_photos' => $entries->filter(fn($e) => $e->photos->count() > 0)->count(),
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Report soil error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate soil report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Pest incidents report
     */
    public function pests(Request $request)
    {
        try {
            // Get the Crop Walk job type
            $cropWalkJobType = JobType::where('name', 'LIKE', '%crop%')->first();

            if (!$cropWalkJobType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Crop Walk job type not found'
                ], 404);
            }

            $query = JobEntry::with([
                'user',
                'greenhouse',
                'bay',
                'values.jobField',
                'photos'
            ])->where('job_type_id', $cropWalkJobType->id);

            $this->applyFilters($query, $request);

            // Get all entries first
            $allEntries = $query->get();

            // Filter for pest incidents (disease_pests = yes)
            $incidents = $allEntries->filter(function ($entry) {
                $diseasePests = $entry->getFieldValue('disease_pests');
                return $diseasePests === 'yes' || $diseasePests === '1' || $diseasePests === true;
            })->values();

            // Process incident data
            $processed = [];
            foreach ($incidents as $incident) {
                $processed[] = [
                    'id' => $incident->id,
                    'job_number' => $incident->job_number,
                    'date' => $incident->submitted_at->format('Y-m-d'),
                    'user' => $incident->user?->name ?? 'Unknown',
                    'greenhouse' => $incident->greenhouse?->name ?? 'Unknown',
                    'bay' => $incident->bay?->name ?? 'Unknown',
                    'plant' => $incident->getFieldValue('plant') ?? 'Unknown',
                    'moisture' => $incident->getFieldValue('moisture_level') ?? 'Unknown',
                    'explanation' => $incident->getFieldValue('explanation') ?? '',
                    'wilting' => $incident->getFieldValue('wilting') === 'yes',
                    'photos' => $incident->photos,
                    'has_photos' => $incident->photos->count() > 0,
                ];
            }

            // Group by location for heatmap
            $locationHeatmap = [];
            foreach ($processed as $incident) {
                $key = $incident['greenhouse'] . ' - ' . $incident['bay'];
                if (!isset($locationHeatmap[$key])) {
                    $locationHeatmap[$key] = 0;
                }
                $locationHeatmap[$key]++;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'incidents' => $processed,
                    'total' => count($processed),
                    'with_photos' => collect($processed)->where('has_photos', true)->count(),
                    'location_heatmap' => $locationHeatmap,
                    'unique_locations' => count($locationHeatmap),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Report pests error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate pest incidents report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Growth tracking report
     */
    public function growth(Request $request)
    {
        try {
            // Get the Growth Tracking job type
            $growthJobType = JobType::where('name', 'LIKE', '%growth%')->first();

            if (!$growthJobType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Growth Tracking job type not found'
                ], 404);
            }

            $query = JobEntry::with([
                'greenhouse',
                'bay',
                'values.jobField',
                'photos'
            ])->where('job_type_id', $growthJobType->id);

            $this->applyFilters($query, $request);

            $entries = $query->orderBy('submitted_at')->get();

            // Group by plant
            $growth = [];
            $allPlants = [];

            foreach ($entries as $entry) {
                $plant = $entry->getFieldValue('plant') ?? 'Unspecified';
                $week = $entry->getFieldValue('week');

                if (!in_array($plant, $allPlants)) {
                    $allPlants[] = $plant;
                }

                if (!isset($growth[$plant])) {
                    $growth[$plant] = [];
                }

                // Group photos by type
                $photos = [
                    'group' => [],
                    'height' => [],
                    'width' => [],
                ];

                foreach ($entry->photos as $photo) {
                    $fieldId = $photo->job_field_id;
                    $field = $entry->values->firstWhere('job_field_id', $fieldId)?->jobField;

                    if ($field) {
                        if (strpos($field->field_name, 'group') !== false) {
                            $photos['group'][] = $photo;
                        } elseif (strpos($field->field_name, 'height') !== false) {
                            $photos['height'][] = $photo;
                        } elseif (strpos($field->field_name, 'width') !== false) {
                            $photos['width'][] = $photo;
                        }
                    }
                }

                $growth[$plant][] = [
                    'id' => $entry->id,
                    'date' => $entry->submitted_at->format('Y-m-d'),
                    'week' => $week,
                    'greenhouse' => $entry->greenhouse?->name ?? 'Unknown',
                    'bay' => $entry->bay?->name ?? 'Unknown',
                    'photos' => $entry->photos,
                    'photos_by_type' => $photos,
                    'has_photos' => $entry->photos->count() > 0,
                    'photo_count' => $entry->photos->count(),
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'entries' => $entries,
                    'growth' => $growth,
                    'plants' => $allPlants,
                    'summary' => [
                        'total_entries' => $entries->count(),
                        'unique_plants' => count($allPlants),
                        'with_photos' => $entries->filter(fn($e) => $e->photos->count() > 0)->count(),
                        'total_photos' => $entries->sum(fn($e) => $e->photos->count()),
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Report growth error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate growth tracking report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export report as CSV
     */
    public function export(Request $request)
    {
        try {
            // Check authentication
            if (!$request->user()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            $type = $request->get('type', 'summary');

            // Get the data based on type
            $data = null;
            switch ($type) {
                case 'chemicals':
                    $response = $this->chemicals($request);
                    $data = $response->getData(true);
                    break;
                case 'soil':
                    $response = $this->soil($request);
                    $data = $response->getData(true);
                    break;
                case 'pests':
                    $response = $this->pests($request);
                    $data = $response->getData(true);
                    break;
                case 'growth':
                    $response = $this->growth($request);
                    $data = $response->getData(true);
                    break;
                default:
                    $response = $this->summary($request);
                    $data = $response->getData(true);
            }

            if (!$data || !isset($data['success']) || !$data['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate export data'
                ], 400);
            }

            // Generate CSV content
            $csv = $this->arrayToCsv($data['data'] ?? []);

            // Generate filename
            $filename = $type . '_report_' . date('Y-m-d_His') . '.csv';

            // Return as CSV download
            return response($csv)
                ->header('Content-Type', 'text/csv; charset=utf-8')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');

        } catch (\Exception $e) {
            Log::error('Export error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to export report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    protected function generateCsv($data)
    {
        $output = fopen('php://temp', 'r+');

        if (isset($data['entries']) && !empty($data['entries'])) {
            $entries = $data['entries'];
            $firstEntry = $entries[0];

            // Headers with names, not IDs
            $headers = [
                'Job #',
                'Date',
                'Worker',
                'Greenhouse',
                'Bay',
                'Job Type',
                'Status',
                'Notes'
            ];

            // Add field labels
            $fieldLabels = [];
            if (isset($firstEntry['values']) && is_array($firstEntry['values'])) {
                foreach ($firstEntry['values'] as $value) {
                    if (isset($value['job_field']['label'])) {
                        $fieldLabels[] = $value['job_field']['label'];
                    }
                }
            }

            $allHeaders = array_merge($headers, $fieldLabels, ['Photos']);
            fputcsv($output, $allHeaders);

            // Add rows
            foreach ($entries as $entry) {
                $row = [];

                // Job #
                $row[] = $entry['job_number'] ?? '';

                // Date
                $row[] = isset($entry['submitted_at']) ? date('Y-m-d', strtotime($entry['submitted_at'])) : '';

                // Worker name
                $row[] = $entry['user']['name'] ?? 'N/A';

                // Greenhouse name
                $row[] = $entry['greenhouse']['name'] ?? 'N/A';

                // Bay name
                $row[] = $entry['bay']['name'] ?? 'N/A';

                // Job Type name
                $row[] = $entry['job_type']['name'] ?? 'N/A';

                // Status
                $row[] = $entry['status'] ?? '';

                // Notes
                $row[] = $entry['notes'] ?? '';

                // Field values
                if (isset($entry['values']) && is_array($entry['values'])) {
                    $valueMap = [];
                    foreach ($entry['values'] as $fieldValue) {
                        if (isset($fieldValue['job_field']['label'])) {
                            $valueMap[$fieldValue['job_field']['label']] = $fieldValue['value'] ?? '';
                        }
                    }

                    foreach ($fieldLabels as $label) {
                        $row[] = $valueMap[$label] ?? '';
                    }
                }

                // Photo count
                $row[] = isset($entry['photos']) ? count($entry['photos']) : 0;

                fputcsv($output, $row);
            }
        } else {
            fputcsv($output, ['No data available']);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    /**
     * Apply common filters to query
     */
    protected function applyFilters($query, $request)
    {
        if ($request->filled('greenhouse_id')) {
            $query->where('greenhouse_id', $request->greenhouse_id);
        }

        if ($request->filled('bay_id')) {
            $query->where('bay_id', $request->bay_id);
        }

        if ($request->filled('job_type_id')) {
            $query->where('job_type_id', $request->job_type_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('submitted_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('submitted_at', '<=', $request->to_date);
        }

        if ($request->filled('plant')) {
            $query->whereHas('values', function ($q) use ($request) {
                $q->whereHas('jobField', fn($qf) => $qf->where('field_name', 'plant'))
                    ->where('value', 'LIKE', '%' . $request->plant . '%');
            });
        }

        return $query;
    }

    /**
     * Convert array to CSV
     */
    protected function arrayToCsv($data, $delimiter = ',', $enclosure = '"')
    {
        $output = fopen('php://temp', 'r+');

        if (empty($data)) {
            fputcsv($output, ['No data available'], $delimiter, $enclosure);
            rewind($output);
            return stream_get_contents($output);
        }

        // Handle entries array
        if (isset($data['entries']) && is_array($data['entries'])) {
            $entries = $data['entries'];

            if (empty($entries)) {
                fputcsv($output, ['No entries found'], $delimiter, $enclosure);
            } else {
                // Get all field labels first
                $fieldLabels = [];
                $firstEntry = $entries[0];

                // Standard headers we want (with names, not IDs)
                $headers = [
                    'Job #',
                    'Date',
                    'Worker',
                    'Greenhouse',
                    'Bay',
                    'Job Type',
                    'Status',
                    'Notes'
                ];

                // Add field labels from job fields
                if (isset($firstEntry['values']) && is_array($firstEntry['values'])) {
                    foreach ($firstEntry['values'] as $fieldValue) {
                        if (isset($fieldValue['job_field']['label'])) {
                            $fieldLabels[] = $fieldValue['job_field']['label'];
                        }
                    }
                }

                $allHeaders = array_merge($headers, $fieldLabels, ['Photos']);
                fputcsv($output, $allHeaders, $delimiter, $enclosure);

                // Add rows with NAMES instead of IDs
                foreach ($entries as $entry) {
                    $row = [];

                    // Job #
                    $row[] = $entry['job_number'] ?? '';

                    // Date
                    $row[] = isset($entry['submitted_at']) ? date('Y-m-d', strtotime($entry['submitted_at'])) : '';

                    // Worker name (not ID)
                    $row[] = $entry['user']['name'] ?? 'N/A';

                    // Greenhouse name (not ID)
                    $row[] = $entry['greenhouse']['name'] ?? 'N/A';

                    // Bay name (not ID)
                    $row[] = $entry['bay']['name'] ?? 'N/A';

                    // Job Type name (not ID)
                    $row[] = $entry['job_type']['name'] ?? 'N/A';

                    // Status
                    $row[] = $entry['status'] ?? '';

                    // Notes
                    $row[] = $entry['notes'] ?? '';

                    // Field values
                    if (isset($entry['values']) && is_array($entry['values'])) {
                        // Create a map of field values by field label
                        $valueMap = [];
                        foreach ($entry['values'] as $fieldValue) {
                            if (isset($fieldValue['job_field']['label'])) {
                                $valueMap[$fieldValue['job_field']['label']] = $fieldValue['value'] ?? '';
                            }
                        }

                        // Add values in the same order as headers
                        foreach ($fieldLabels as $label) {
                            $row[] = $valueMap[$label] ?? '';
                        }
                    }

                    // Photo count
                    $row[] = isset($entry['photos']) ? count($entry['photos']) : 0;

                    fputcsv($output, $row, $delimiter, $enclosure);
                }
            }
        } else {
            // Handle flat data
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $value = json_encode($value);
                }
                fputcsv($output, [$key, $value], $delimiter, $enclosure);
            }
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }
    /**
     * Write flattened array to CSV
     */
    protected function writeFlattenedArray($output, $array, $prefix = '', $delimiter = ',', $enclosure = '"')
    {
        foreach ($array as $key => $value) {
            $newKey = $prefix ? $prefix . '.' . $key : $key;

            if (is_array($value)) {
                $this->writeFlattenedArray($output, $value, $newKey, $delimiter, $enclosure);
            } else {
                fputcsv($output, [$newKey, $value], $delimiter, $enclosure);
            }
        }
    }
}