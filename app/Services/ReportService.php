<?php
// app/Services/ReportService.php

namespace App\Services;

use App\Models\JobEntry;
use App\Models\JobType;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function generateChemicalReport($filters = [])
    {
        $query = JobEntry::with(['user', 'greenhouse', 'bay', 'values'])
            ->whereHas('jobType', fn($q) => $q->where('name', 'Chemicals'));

        $this->applyFilters($query, $filters);

        $entries = $query->get();

        return [
            'total_applications' => $entries->count(),
            'by_chemical' => $this->groupByChemical($entries),
            'by_plant' => $this->groupByPlant($entries),
            'trends' => $this->calculateTrends($entries),
            'entries' => $entries
        ];
    }

    public function generateSoilTrends($filters = [])
    {
        // Implementation
        return [];
    }

    public function generatePestReport($filters = [])
    {
        // Implementation
        return [];
    }

    protected function applyFilters($query, $filters)
    {
        if (!empty($filters['greenhouse_id'])) {
            $query->where('greenhouse_id', $filters['greenhouse_id']);
        }

        if (!empty($filters['from_date'])) {
            $query->whereDate('submitted_at', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $query->whereDate('submitted_at', '<=', $filters['to_date']);
        }

        return $query;
    }

    protected function groupByChemical($entries)
    {
        $chemicals = [];
        foreach ($entries as $entry) {
            $chem = $entry->getFieldValue('chemical_applied');
            if (!isset($chemicals[$chem])) {
                $chemicals[$chem] = [
                    'name' => $chem,
                    'count' => 0,
                    'total_oz' => 0
                ];
            }
            $chemicals[$chem]['count']++;
            $chemicals[$chem]['total_oz'] += floatval($entry->getFieldValue('oz_per_100_gallons') ?? 0);
        }
        return $chemicals;
    }

    protected function groupByPlant($entries)
    {
        // Implementation
        return [];
    }

    protected function calculateTrends($entries)
    {
        // Implementation
        return [];
    }
}