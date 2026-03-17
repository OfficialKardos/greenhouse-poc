@forelse($jobs as $job)
    <div class="job-card {{ $job['status'] ?? 'submitted' }}">
        <div class="job-header">
            <span class="job-number">#{{ $job['job_number'] ?? $job['id'] }}</span>
            <span class="job-status status-{{ $job['status'] ?? 'submitted' }}">
                {{ ucfirst($job['status'] ?? 'submitted') }}
            </span>
        </div>
        
        <div class="job-details">
            <div class="job-detail-item">
                <span>📅</span> {{ \Carbon\Carbon::parse($job['submitted_at'] ?? $job['created_at'])->format('M d, Y') }}
            </div>
            <div class="job-detail-item">
                <span>🌿</span> {{ $job['greenhouse']['name'] ?? 'N/A' }}
            </div>
            <div class="job-detail-item">
                <span>📋</span> {{ $job['job_type']['name'] ?? $job['job_type_name'] ?? 'N/A' }}
            </div>
            <div class="job-detail-item">
                <span>📍</span> {{ $job['bay']['name'] ?? $job['bay_name'] ?? 'N/A' }}
            </div>
        </div>
        
        @if(isset($job['photos_count']) && $job['photos_count'] > 0)
            <div style="margin-bottom: 8px; font-size: 12px; color: #64748b;">
                📸 {{ $job['photos_count'] }} photo(s)
            </div>
        @endif
        
        <div class="job-actions">
            <button class="job-action-btn edit-btn" onclick="editJob({{ $job['id'] }})">
                <span>✏️</span> Edit
            </button>
            <button class="job-action-btn view-btn" onclick="viewJob({{ $job['id'] }})">
                <span>👁️</span> View
            </button>
        </div>
    </div>
@empty
    <div class="empty-state">
        <span class="icon">📋</span>
        <p>No jobs found</p>
        <p style="font-size: 12px; margin-top: 8px;">Try adjusting your filters</p>
    </div>
@endforelse

<script>
    function viewJob(id) {
    window.location.href = `/mobile/jobs/${id}/view`;
}
</script>