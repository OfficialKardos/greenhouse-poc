<script>
console.log('Edit form template loaded');
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, checking for file input...');
    console.log('File input exists:', !!document.getElementById('new-photos'));
});
</script><script src="{{ asset('js/edit-form.js') }}"></script>

<form action="/mobile/jobs/{{ $job->id }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="values_present" value="1">
    
    <!-- Existing Photos Section -->
    @if($job->photos->count() > 0)
    <div class="form-group">
        <label class="form-label">Existing Photos</label>
        <div class="existing-photos-grid">
            @foreach($job->photos as $photo)
                <div class="photo-item" onclick="viewPhoto('{{ $photo->url }}')">
                    <img src="{{ $photo->thumbnail_url ?? $photo->url }}" 
                         alt="Job photo"
                         class="existing-photo">
                    <button type="button" 
                            class="delete-photo-btn" 
                            onclick="deletePhoto({{ $photo->id }}, this)"
                            title="Delete photo">
                        ×
                    </button>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- New Photo Upload Section -->
    <div class="form-group">
        <label class="form-label">Add New Photos</label>
        <div class="photo-upload" onclick="document.getElementById('new-photos').click()">
            📸 Tap to add photos (max 5MB each)
        </div>
        <input type="file" 
            id="new-photos" 
            name="new_photos[]" 
            accept="image/*"
            multiple
            style="display: none;">
        <div id="new-photos-preview" class="new-photos-preview"></div>
        <div id="photo-error" class="error-message" style="display: none; color: #ef4444; font-size: 12px; margin-top: 5px;"></div>
    </div>

    <!-- Form Fields -->
    @foreach($fields as $field)
        <div class="form-group">
            <label class="form-label">
                {{ $field->label }}
                @if($field->is_required)
                    <span class="required-star">*</span>
                @endif
            </label>
            
            @php
                $currentValue = $field->current_value;
            @endphp
            
            @if($field->field_type === 'text')
                <input type="text" 
                       name="values[{{ $field->id }}]" 
                       class="form-input"
                       value="{{ $currentValue ?? '' }}"
                       {{ $field->is_required ? 'required' : '' }}>
                       
            @elseif($field->field_type === 'textarea')
                <textarea name="values[{{ $field->id }}]" 
                          class="form-input" 
                          rows="3"
                          {{ $field->is_required ? 'required' : '' }}>{{ $currentValue ?? '' }}</textarea>
                          
            @elseif($field->field_type === 'number' || $field->field_type === 'temperature' || $field->field_type === 'ppm' || $field->field_type === 'ph' || $field->field_type === 'ec')
                <input type="number" 
                       name="values[{{ $field->id }}]" 
                       class="form-input"
                       step="any"
                       value="{{ $currentValue ?? '' }}"
                       {{ $field->is_required ? 'required' : '' }}>
                       
            @elseif($field->field_type === 'date')
                <input type="date" 
                       name="values[{{ $field->id }}]" 
                       class="form-input"
                       value="{{ $currentValue ?? date('Y-m-d') }}"
                       {{ $field->is_required ? 'required' : '' }}>
                       
            @elseif($field->field_type === 'yes_no')
                <div class="radio-group">
                    <label class="radio-label">
                        <input type="radio" 
                               name="values[{{ $field->id }}]" 
                               value="yes"
                               {{ ($currentValue ?? '') === 'yes' ? 'checked' : '' }}
                               {{ $field->is_required ? 'required' : '' }}>
                        <span>Yes</span>
                    </label>
                    <label class="radio-label">
                        <input type="radio" 
                               name="values[{{ $field->id }}]" 
                               value="no"
                               {{ ($currentValue ?? '') === 'no' ? 'checked' : '' }}>
                        <span>No</span>
                    </label>
                </div>
                
            @elseif($field->field_type === 'dropdown' && $field->dropdownSource)
                <select name="values[{{ $field->id }}]" 
                        class="form-select"
                        {{ $field->is_required ? 'required' : '' }}>
                    <option value="">Select {{ $field->label }}</option>
                    @foreach($field->dropdownSource->values as $option)
                        <option value="{{ $option->value }}" 
                                {{ ($currentValue ?? '') == $option->value ? 'selected' : '' }}>
                            {{ $option->label ?? $option->value }}
                        </option>
                    @endforeach
                </select>
            @endif
        </div>
    @endforeach
    
    <!-- Notes Field -->
    <div class="form-group">
        <label class="form-label">Notes</label>
        <textarea name="notes" class="form-input" rows="3">{{ $job->notes }}</textarea>
    </div>
    
    <!-- Form Actions -->
    <div class="form-actions">
        <button type="submit" class="btn-primary update-btn">
            <span>💾</span> Update Job
        </button>
        <button type="button" class="btn-secondary cancel-btn" onclick="closeEditModal()">
            <span>✕</span> Cancel
        </button>
    </div>
</form>

<!-- Hidden input for deleted photos -->
<input type="hidden" name="deleted_photos" id="deleted-photos" value="">


<style>
.existing-photos-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    margin-bottom: 15px;
}

.existing-photos-grid .photo-item {
    position: relative;
    aspect-ratio: 1;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
}

.existing-photo {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.delete-photo-btn {
    position: absolute;
    top: 4px;
    right: 4px;
    width: 24px;
    height: 24px;
    background: rgba(239, 68, 68, 0.9);
    color: white;
    border: none;
    border-radius: 50%;
    font-size: 18px;
    line-height: 1;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
}

.delete-photo-btn:hover {
    background: #dc2626;
}

.new-photos-preview {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    margin-top: 10px;
}

.new-photo-preview-item {
    position: relative;
    aspect-ratio: 1;
    border-radius: 8px;
    overflow: hidden;
}

.new-photo-preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.new-photo-badge {
    position: absolute;
    top: 4px;
    right: 4px;
    background: #10b981;
    color: white;
    padding: 2px 6px;
    border-radius: 12px;
    font-size: 10px;
    font-weight: bold;
}

.photo-upload {
    border: 2px dashed #cbd5e0;
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    color: #64748b;
    font-size: 14px;
    transition: all 0.2s;
}

.photo-upload:hover {
    border-color: #10b981;
    background: #f0fdf4;
}

.radio-group {
    display: flex;
    gap: 20px;
    padding: 8px 0;
}

.radio-label {
    display: flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
}

.radio-label input[type="radio"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.required-star {
    color: #ef4444;
    margin-left: 2px;
}

.form-actions {
    display: flex;
    gap: 12px;
    margin-top: 24px;
}

.update-btn {
    flex: 2;
    background: #10b981;
}

.cancel-btn {
    flex: 1;
    background: #64748b;
}

.btn-secondary {
    padding: 14px;
    border: none;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.error-message {
    color: #ef4444;
    font-size: 12px;
    margin-top: 5px;
}
</style>