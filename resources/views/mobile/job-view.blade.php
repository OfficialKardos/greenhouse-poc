@extends('mobile.layout')

@section('title', 'Job Details')

@section('content')
<div class="btn-back" onclick="window.history.back()">
    <span>←</span> Back
</div>

<div class="job-view-header">
    <div class="job-view-title">
        <h1>Job #{{ $job->job_number }}</h1>
        <span class="status-badge status-{{ $job->status }}">
            {{ ucfirst($job->status) }}
        </span>
    </div>
    <p class="job-view-meta">
        Submitted: {{ \Carbon\Carbon::parse($job->submitted_at)->format('M d, Y h:i A') }}
    </p>
</div>

<!-- Location Info Card -->
<div class="info-card">
    <h3 class="info-card-title">
        <span>📍</span> Location
    </h3>
    <div class="info-grid">
        <div class="info-item">
            <span class="info-label">Greenhouse:</span>
            <span class="info-value">{{ $job->greenhouse->name ?? 'N/A' }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Bay:</span>
            <span class="info-value">{{ $job->bay->name ?? 'N/A' }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Job Type:</span>
            <span class="info-value">{{ $job->jobType->name ?? 'N/A' }}</span>
        </div>
    </div>
</div>

<!-- Job Details Card -->
<div class="info-card">
    <h3 class="info-card-title">
        <span>📋</span> Job Details
    </h3>
    <div class="details-list">
        @foreach($job->values as $value)
            <div class="detail-row">
                <span class="detail-label">{{ $value->jobField->label ?? 'Field' }}:</span>
                <span class="detail-value">
                    @if($value->jobField && $value->jobField->field_type === 'yes_no')
                        {{ $value->value === 'yes' ? '✅ Yes' : '❌ No' }}
                    @else
                        {{ $value->value ?: '—' }}
                    @endif
                </span>
            </div>
        @endforeach
        
        @if($job->notes)
            <div class="detail-row notes-row">
                <span class="detail-label">Notes:</span>
                <span class="detail-value notes-text">{{ $job->notes }}</span>
            </div>
        @endif
    </div>
</div>

<!-- Photos Card -->
@if($job->photos->count() > 0)
<div class="info-card">
    <h3 class="info-card-title">
        <span>📸</span> Photos ({{ $job->photos->count() }})
    </h3>
    <div class="photo-grid">
        @foreach($job->photos as $photo)
            <div class="photo-item" onclick="viewPhoto('{{ $photo->url }}')">
                <img src="{{ $photo->thumbnail_url ?? $photo->url }}" 
                     alt="Job photo"
                     class="photo-thumbnail"
                     loading="lazy">
                @if($photo->job_field_id)
                    @php
                        $field = $job->values->firstWhere('job_field_id', $photo->job_field_id);
                    @endphp
                    @if($field && $field->jobField)
                        <span class="photo-label">{{ $field->jobField->label }}</span>
                    @endif
                @endif
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- Action Buttons -->
<div class="action-buttons">
    @if($job->status === 'submitted')
        <button class="action-btn edit-btn" onclick="editJob({{ $job->id }})">
            <span>✏️</span> Edit Job
        </button>
    @endif
    <button class="action-btn back-btn" onclick="window.location.href='/mobile/submitted'">
        <span>←</span> Go to Jobs
    </button>
</div>

<!-- Edit Modal (same as before) -->
<div id="edit-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Job #{{ $job->job_number }}</h3>
            <button class="close-btn" onclick="closeEditModal()">&times;</button>
        </div>
        <div id="edit-form-container" class="modal-body">
            <!-- Edit form will be loaded here -->
        </div>
    </div>
</div>

<!-- Photo Viewer Modal -->
<div id="photo-modal" class="modal photo-viewer" style="display: none;" onclick="closePhotoViewer()">
    <div class="photo-viewer-content" onclick="event.stopPropagation()">
        <img id="photo-viewer-img" src="" alt="Full size photo">
        <button class="close-photo-btn" onclick="closePhotoViewer()">&times;</button>
        <a id="download-photo-btn" href="#" download class="download-photo-btn">
            📥 Download
        </a>
    </div>
</div>
@endsection

@section('nav')
    <div class="nav-bar">
        <div class="nav-item" onclick="window.location.href='/mobile/dashboard'">
            <span>🏠</span>
            Home
        </div>
        <div class="nav-item" onclick="window.location.href='/mobile/submitted'">
            <span>📋</span>
            Jobs
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
.job-view-header {
    background: white;
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.job-view-title {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.job-view-title h1 {
    font-size: 20px;
    color: #1e293b;
    margin: 0;
}

.job-view-meta {
    color: #64748b;
    font-size: 13px;
    margin: 0;
}

.status-badge {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
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

.info-card {
    background: white;
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.info-card-title {
    margin: 0 0 16px 0;
    font-size: 16px;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 8px;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 12px;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.info-label {
    font-size: 12px;
    color: #64748b;
}

.info-value {
    font-size: 14px;
    color: #1e293b;
    font-weight: 500;
}

.details-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.detail-row {
    display: flex;
    padding: 8px 0;
    border-bottom: 1px solid #f1f5f9;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    width: 40%;
    font-size: 13px;
    color: #64748b;
}

.detail-value {
    width: 60%;
    font-size: 13px;
    color: #1e293b;
    font-weight: 500;
    word-break: break-word;
}

.notes-row {
    flex-direction: column;
    gap: 8px;
}

.notes-text {
    width: 100%;
    background: #f8fafc;
    padding: 12px;
    border-radius: 8px;
    font-size: 13px;
    line-height: 1.5;
}

.photo-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 12px;
}

.photo-item {
    position: relative;
    cursor: pointer;
    border-radius: 8px;
    overflow: hidden;
    background: #f1f5f9;
    aspect-ratio: 1;
}

.photo-thumbnail {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.2s;
}

.photo-item:hover .photo-thumbnail {
    transform: scale(1.05);
}

.photo-label {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0,0,0,0.6);
    color: white;
    font-size: 10px;
    padding: 4px;
    text-align: center;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.action-buttons {
    display: flex;
    gap: 12px;
    margin-top: 20px;
}

.action-btn {
    flex: 1;
    padding: 14px;
    border: none;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.edit-btn {
    background: #3b82f6;
    color: white;
}

.back-btn {
    background: #f1f5f9;
    color: #475569;
}

/* Photo Viewer Modal */
.photo-viewer .modal-content {
    background: transparent;
    max-width: 90vw;
    max-height: 90vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.photo-viewer-content {
    position: relative;
    max-width: 100%;
    max-height: 100%;
}

.photo-viewer-content img {
    max-width: 100%;
    max-height: 90vh;
    object-fit: contain;
    border-radius: 8px;
}

.close-photo-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 40px;
    height: 40px;
    background: rgba(0,0,0,0.6);
    color: white;
    border: none;
    border-radius: 50%;
    font-size: 24px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.download-photo-btn {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background: #10b981;
    color: white;
    padding: 10px 20px;
    border-radius: 20px;
    text-decoration: none;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 5px;
}
</style>

<script>
function editJob(id) {
    fetch(`/mobile/jobs/${id}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(html => {
        document.getElementById('edit-form-container').innerHTML = html;
        document.getElementById('edit-modal').style.display = 'flex';
    })
    .catch(error => {
        console.error('Edit error:', error);
        alert('Failed to load edit form');
    });
}

function closeEditModal() {
    document.getElementById('edit-modal').style.display = 'none';
    document.getElementById('edit-form-container').innerHTML = '';
}

function viewPhoto(url) {
    document.getElementById('photo-viewer-img').src = url;
    document.getElementById('download-photo-btn').href = url;
    document.getElementById('photo-modal').style.display = 'flex';
}

function closePhotoViewer() {
    document.getElementById('photo-modal').style.display = 'none';
    document.getElementById('photo-viewer-img').src = '';
}

function submitEdit(event, id) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    
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
            window.location.reload(); // Refresh to show updated data
        } else {
            alert(data.message || 'Failed to update job');
        }
    })
    .catch(error => {
        console.error('Update error:', error);
        alert('Failed to update job');
    });
}
</script>