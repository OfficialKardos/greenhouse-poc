@extends('mobile.layout')

@section('title', 'Submitted Jobs')

@section('content')
<div class="btn-back" onclick="window.history.back()">
    <span>←</span> Back
</div>

<div class="header">
    <h1>Submitted Jobs</h1>
    <p>View and manage your job submissions</p>
</div>

<!-- Date Filters -->
<div class="filters-section">
    <div class="filter-row">
        <div class="filter-group">
            <label class="filter-label">From Date</label>
            <input type="date" id="from_date" class="filter-input" value="{{ request('from_date', now()->subDays(7)->format('Y-m-d')) }}">
        </div>
        <div class="filter-group">
            <label class="filter-label">To Date</label>
            <input type="date" id="to_date" class="filter-input" value="{{ request('to_date', now()->format('Y-m-d')) }}">
        </div>
    </div>
    <div class="filter-row">
        <div class="filter-group">
            <label class="filter-label">Job Type</label>
            <select id="job_type" class="filter-input">
                <option value="">All Job Types</option>
                @foreach($jobTypes ?? [] as $type)
                    <option value="{{ $type['id'] }}">{{ $type['name'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label class="filter-label">Status</label>
            <select id="status" class="filter-input">
                <option value="">All Status</option>
                <option value="submitted">Submitted</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
    </div>
    <div class="filter-actions">
        <button class="filter-btn apply-btn" onclick="applyFilters()">
            <span>🔍</span> Apply Filters
        </button>
        <button class="filter-btn reset-btn" onclick="resetFilters()">
            <span>🔄</span> Reset
        </button>
    </div>
</div>

<!-- Jobs List -->
<div id="jobs-list" class="jobs-list">
    @include('mobile.partials.jobs-list', ['jobs' => $jobs])
</div>

<!-- Loading Spinner (hidden by default) -->
<div id="loading-spinner" class="loading-spinner" style="display: none;">
    <div class="spinner"></div>
    <p>Loading...</p>
</div>

<!-- Edit Job Modal -->
<div id="edit-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Job</h3>
            <button class="close-btn" onclick="closeEditModal()">&times;</button>
        </div>
        <div id="edit-form-container" class="modal-body">
            <!-- Form will be loaded here -->
        </div>
    </div>
</div>
@endsection

@section('nav')
    <div class="nav-bar">
        <div class="nav-item" onclick="window.location.href='/mobile/dashboard'">
            <span>🏠</span>
            Home
        </div>
        <div class="nav-item active">
            <span>📋</span>
            Submitted
        </div>
        <div class="nav-item" onclick="document.getElementById('logout-form').submit()">
            <span>🚪</span>
            Logout
        </div>
    </div>
    <form id="logout-form" action="{{ route('mobile.logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
@endsection

<style>
.filters-section {
    background: white;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.filter-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 12px;
}

.filter-group {
    display: flex;
    flex-direction: column;
}

.filter-label {
    font-size: 12px;
    color: #64748b;
    margin-bottom: 4px;
}

.filter-input {
    padding: 10px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    background: white;
}

.filter-actions {
    display: flex;
    gap: 10px;
    margin-top: 8px;
}

.filter-btn {
    flex: 1;
    padding: 12px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.apply-btn {
    background: #10b981;
    color: white;
}

.reset-btn {
    background: #f1f5f9;
    color: #64748b;
}

.jobs-list {
    min-height: 200px;
}

.job-card {
    background: white;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border-left: 4px solid transparent;
}

.job-card.submitted { border-left-color: #f59e0b; }
.job-card.approved { border-left-color: #10b981; }
.job-card.rejected { border-left-color: #ef4444; }

.job-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.job-number {
    font-weight: 600;
    color: #1e293b;
    font-size: 16px;
}

.job-status {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-submitted {
    background: #fef3c7;
    color: #b45309;
}

.status-approved {
    background: #d1fae5;
    color: #065f46;
}

.status-rejected {
    background: #fee2e2;
    color: #b91c1c;
}

.job-details {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    margin-bottom: 12px;
    font-size: 13px;
    color: #475569;
}

.job-detail-item {
    display: flex;
    align-items: center;
    gap: 4px;
}

.job-actions {
    display: flex;
    gap: 8px;
    border-top: 1px solid #e2e8f0;
    padding-top: 12px;
}

.job-action-btn {
    flex: 1;
    padding: 8px;
    border: none;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
}

.edit-btn {
    background: #3b82f6;
    color: white;
}

.view-btn {
    background: #f1f5f9;
    color: #475569;
}

.loading-spinner {
    text-align: center;
    padding: 40px;
}

.spinner {
    width: 40px;
    height: 40px;
    border: 3px solid #e2e8f0;
    border-top-color: #10b981;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 10px;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Modal Styles */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    padding: 16px;
}

.modal-content {
    background: white;
    border-radius: 16px;
    max-width: 500px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    padding: 16px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: sticky;
    top: 0;
    background: white;
}

.modal-header h3 {
    margin: 0;
    color: #1e293b;
}

.close-btn {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #64748b;
}

.modal-body {
    padding: 20px;
}

.empty-state {
    text-align: center;
    padding: 40px;
    color: #94a3b8;
}

.empty-state .icon {
    font-size: 48px;
    margin-bottom: 10px;
    display: block;
}
</style>

<script>
function applyFilters() {
    const fromDate = document.getElementById('from_date').value;
    const toDate = document.getElementById('to_date').value;
    const jobType = document.getElementById('job_type').value;
    const status = document.getElementById('status').value;
    
    showLoading();
    
    fetch(`/mobile/submitted/filter?from_date=${fromDate}&to_date=${toDate}&job_type=${jobType}&status=${status}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(html => {
        document.getElementById('jobs-list').innerHTML = html;
        hideLoading();
    })
    .catch(error => {
        console.error('Filter error:', error);
        hideLoading();
        alert('Failed to apply filters');
    });
}

function resetFilters() {
    document.getElementById('from_date').value = '{{ now()->subDays(7)->format('Y-m-d') }}';
    document.getElementById('to_date').value = '{{ now()->format('Y-m-d') }}';
    document.getElementById('job_type').value = '';
    document.getElementById('status').value = '';
    applyFilters();
}

function showLoading() {
    document.getElementById('loading-spinner').style.display = 'block';
    document.getElementById('jobs-list').style.opacity = '0.5';
}

function hideLoading() {
    document.getElementById('loading-spinner').style.display = 'none';
    document.getElementById('jobs-list').style.opacity = '1';
}

function viewJob(id) {
    window.location.href = `/mobile/jobs/${id}`;
}

function editJob(id) {
    showLoading();
    
    fetch(`/mobile/jobs/${id}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(html => {
        document.getElementById('edit-form-container').innerHTML = html;
        document.getElementById('edit-modal').style.display = 'flex';
        hideLoading();
    })
    .catch(error => {
        console.error('Edit error:', error);
        hideLoading();
        alert('Failed to load edit form');
    });
}

function closeEditModal() {
    document.getElementById('edit-modal').style.display = 'none';
    document.getElementById('edit-form-container').innerHTML = '';
}

function submitEdit(event, id) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    
    showLoading();
    
    fetch(`/mobile/jobs/${id}`, {
        method: 'PUT',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeEditModal();
            applyFilters(); // Refresh the list
        } else {
            alert(data.message || 'Failed to update job');
        }
        hideLoading();
    })
    .catch(error => {
        console.error('Update error:', error);
        hideLoading();
        alert('Failed to update job');
    });
}
</script>