@extends('mobile.layout')

@section('title', 'Job Form')

@section('content')
<div class="btn-back" onclick="window.history.back()">
        <span>←</span> Back
    </div>
    <div class="header">
        <h1>Complete Job</h1>
        <p>Fill in the details below</p>
    </div>
    
    <form method="POST" action="{{ route('mobile.submit') }}" 
      enctype="multipart/form-data" id="jobForm">
    @csrf
    
    <input type="hidden" name="greenhouse_id" value="{{ $greenhouseId }}">
    <input type="hidden" name="bay_id" value="{{ $bayId }}">
    <input type="hidden" name="job_type_id" value="{{ $jobTypeId }}">
    
    <!-- Regular fields -->
    @foreach($fields as $field)
        <div class="form-group">
            <label class="form-label">
                {{ $field['label'] }}
                @if($field['is_required'])
                    <span style="color: #e53e3e;">*</span>
                @endif
            </label>
            
            @if($field['field_type'] === 'text')
                <input type="text" 
                       name="values[{{ $field['id'] }}]" 
                       class="form-input"
                       value="{{ old('values.' . $field['id']) }}"
                       {{ $field['is_required'] ? 'required' : '' }}>
                
            @elseif($field['field_type'] === 'textarea')
                <textarea name="values[{{ $field['id'] }}]" 
                          class="form-input" 
                          rows="3"
                          {{ $field['is_required'] ? 'required' : '' }}>{{ old('values.' . $field['id']) }}</textarea>
                
            @elseif($field['field_type'] === 'number' || $field['field_type'] === 'temperature' || $field['field_type'] === 'ppm' || $field['field_type'] === 'ph' || $field['field_type'] === 'ec')
                <input type="number" 
                       name="values[{{ $field['id'] }}]" 
                       class="form-input"
                       step="any"
                       value="{{ old('values.' . $field['id']) }}"
                       {{ $field['is_required'] ? 'required' : '' }}>
                
            @elseif($field['field_type'] === 'date')
                <input type="date" 
                       name="values[{{ $field['id'] }}]" 
                       class="form-input"
                       value="{{ old('values.' . $field['id'], date('Y-m-d')) }}"
                       {{ $field['is_required'] ? 'required' : '' }}>
                
            @elseif($field['field_type'] === 'yes_no')
                <div class="radio-group">
                    <label>
                        <input type="radio" 
                               name="values[{{ $field['id'] }}]" 
                               value="yes"
                               {{ old('values.' . $field['id']) === 'yes' ? 'checked' : '' }}
                               {{ $field['is_required'] ? 'required' : '' }}> Yes
                    </label>
                    <label>
                        <input type="radio" 
                               name="values[{{ $field['id'] }}]" 
                               value="no"
                               {{ old('values.' . $field['id']) === 'no' ? 'checked' : '' }}> No
                    </label>
                </div>
                
            @elseif($field['field_type'] === 'dropdown' && isset($field['dropdown_values']))
                <select name="values[{{ $field['id'] }}]" 
                        class="form-select"
                        {{ $field['is_required'] ? 'required' : '' }}>
                    <option value="">Select {{ $field['label'] }}</option>
                    @foreach($field['dropdown_values'] as $option)
                        <option value="{{ $option['value'] }}" 
                                {{ old('values.' . $field['id']) == $option['value'] ? 'selected' : '' }}>
                            {{ $option['label'] ?? $option['value'] }}
                        </option>
                    @endforeach
                </select>
                
            @elseif($field['field_type'] === 'photo')
                <div class="form-group">
                    <!-- Hidden field to track if photo was uploaded -->
                    <input type="hidden" name="has_photo[{{ $field['id'] }}]" id="has_photo_{{ $field['id'] }}" value="0">
                    
                    <!-- Photo upload button -->
                    <div class="photo-upload" id="upload-btn-{{ $field['id'] }}" 
                         onclick="document.getElementById('file-{{ $field['id'] }}').click()">
                        📸 Tap to take a photo
                        <p style="font-size: 12px; color: #718096; margin-top: 5px;">JPG or PNG, max 5MB</p>
                    </div>
                    
                    <!-- File input -->
                    <input type="file" 
                           id="file-{{ $field['id'] }}" 
                           name="photos[{{ $field['id'] }}]" 
                           accept="image/*"
                           capture="environment"
                           style="display: none;"
                           onchange="handlePhotoSelect(this, {{ $field['id'] }})">
                    
                    <!-- Preview container -->
                    <div id="preview-container-{{ $field['id'] }}" style="display: none; margin-top: 10px;">
                        <img id="preview-{{ $field['id'] }}" src="" class="photo-preview">
                        <div style="display: flex; gap: 10px; margin-top: 5px;">
                            <button type="button" class="btn-primary" style="background: #e53e3e; flex: 1;" onclick="removePhoto({{ $field['id'] }})">
                                Remove
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endforeach
    
    <button type="submit" class="btn-primary" id="submit-btn">Submit Job</button>
</form>
@endsection

@section('nav')
    <div class="nav-bar">
        <div class="nav-item" onclick="window.location.href='/mobile/dashboard'">
            <span>🏠</span>
            Home
        </div>
        <div class="nav-item active">
            <span>📝</span>
            Form
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

<script>
// Store uploaded files in memory
const uploadedFiles = {};

function handlePhotoSelect(input, fieldId) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validate file size (5MB max)
        if (file.size > 10 * 1024 * 1024) {
            alert('File too large. Maximum size is 5MB.');
            input.value = '';
            return;
        }
        
        // Validate file type
        if (!file.type.match('image.*')) {
            alert('Please select an image file.');
            input.value = '';
            return;
        }
        
        // Store file in memory
        uploadedFiles[fieldId] = file;
        
        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-' + fieldId).src = e.target.result;
            document.getElementById('preview-container-' + fieldId).style.display = 'block';
            document.getElementById('upload-btn-' + fieldId).style.display = 'none';
            document.getElementById('has_photo_' + fieldId).value = '1';
        };
        reader.readAsDataURL(file);
    }
}

function removePhoto(fieldId) {
    // Clear file
    delete uploadedFiles[fieldId];
    document.getElementById('file-' + fieldId).value = '';
    document.getElementById('has_photo_' + fieldId).value = '0';
    
    // Hide preview
    document.getElementById('preview-container-' + fieldId).style.display = 'none';
    document.getElementById('preview-' + fieldId).src = '';
    
    // Show upload button
    document.getElementById('upload-btn-' + fieldId).style.display = 'block';
}

// Handle form submission with files
document.getElementById('jobForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    
    // Add files to formData
    for (const [fieldId, file] of Object.entries(uploadedFiles)) {
        formData.append('photos[' + fieldId + ']', file);
    }
    
    // Disable submit button
    const submitBtn = document.getElementById('submit-btn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = 'Submitting...';
    
    // Submit via fetch to handle files properly
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '/mobile/dashboard?success=Job+submitted+successfully';
        } else {
            alert('Error: ' + (data.message || 'Failed to submit job'));
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Submit Job';
        }
    })
    .catch(error => {
        console.error('Submission error:', error);
        
        // Try regular form submit as fallback
        form.submit();
    });
});
</script>