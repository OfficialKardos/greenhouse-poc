// Global functions for edit form
window.initEditForm = function() {
    console.log('Edit form initialized');
    
    // Set up file input handler
    const fileInput = document.getElementById('new-photos');
    if (fileInput) {
        console.log('File input found, attaching event');
        
        // Remove any existing listeners and attach new one
        fileInput.onchange = function(event) {
            console.log('File input changed', event);
            previewPhotos(this);
        };
    } else {
        console.log('File input not found');
    }
};

function previewPhotos(input) {
    console.log('previewPhotos called', input);
    
    const preview = document.getElementById('new-photos-preview');
    const errorDiv = document.getElementById('photo-error');
    
    if (!preview) {
        console.error('Preview element not found');
        return;
    }
    
    preview.innerHTML = '';
    if (errorDiv) errorDiv.style.display = 'none';
    
    if (!input.files || input.files.length === 0) {
        console.log('No files selected');
        return;
    }
    
    console.log('Processing', input.files.length, 'files');
    
    for (let i = 0; i < input.files.length; i++) {
        const file = input.files[i];
        console.log('File', i, file.name, file.type, file.size);
        
        // Check file size (5MB max)
        if (file.size > 5 * 1024 * 1024) {
            if (errorDiv) {
                errorDiv.textContent = 'File ' + file.name + ' exceeds 5MB limit';
                errorDiv.style.display = 'block';
            }
            input.value = '';
            preview.innerHTML = '';
            return;
        }
        
        // Check file type
        if (!file.type.startsWith('image/')) {
            if (errorDiv) {
                errorDiv.textContent = 'File ' + file.name + ' is not an image';
                errorDiv.style.display = 'block';
            }
            input.value = '';
            preview.innerHTML = '';
            return;
        }
        
        const reader = new FileReader();
        
        reader.onload = function(e) {
            console.log('File loaded', e.target.result.substring(0, 50) + '...');
            const div = document.createElement('div');
            div.className = 'new-photo-preview-item';
            div.innerHTML = '<img src="' + e.target.result + '" alt="New photo"><span class="new-photo-badge">NEW</span>';
            preview.appendChild(div);
        };
        
        reader.readAsDataURL(file);
    }
}

window.viewPhoto = function(url) {
    console.log('viewPhoto called', url);
    
    let viewer = document.getElementById('photo-viewer-modal');
    if (!viewer) {
        viewer = document.createElement('div');
        viewer.id = 'photo-viewer-modal';
        viewer.style.cssText = 'position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.9); display:flex; align-items:center; justify-content:center; z-index:2000;';
        viewer.innerHTML = '<div style="position:relative; max-width:90vw; max-height:90vh;"><img id="viewer-img" src="" style="max-width:100%; max-height:90vh; object-fit:contain;"><button onclick="this.parentElement.parentElement.remove()" style="position:absolute; top:10px; right:10px; width:40px; height:40px; background:white; border:none; border-radius:50%; font-size:24px; cursor:pointer;">&times;</button></div>';
        document.body.appendChild(viewer);
    }
    document.getElementById('viewer-img').src = url;
    viewer.style.display = 'flex';
};

window.deletePhoto = function(photoId, btn) {
    console.log('deletePhoto called', photoId);
    
    if (confirm('Are you sure you want to delete this photo?')) {
        let deletedPhotoIds = [];
        const deletedInput = document.getElementById('deleted-photos');
        
        if (deletedInput.value) {
            try {
                deletedPhotoIds = JSON.parse(deletedInput.value);
                console.log('Existing deleted photos', deletedPhotoIds);
            } catch (e) {
                deletedPhotoIds = [];
            }
        }
        
        deletedPhotoIds.push(photoId);
        deletedInput.value = JSON.stringify(deletedPhotoIds);
        console.log('Updated deleted photos', deletedPhotoIds);
        
        const photoItem = btn.closest('.photo-item');
        if (photoItem) {
            photoItem.remove();
        }
    }
};

window.closeEditModal = function() {
    console.log('closeEditModal called');
    
    const modal = document.getElementById('edit-modal');
    if (modal) {
        modal.style.display = 'none';
    } else if (window.parent && window.parent.document.getElementById('edit-modal')) {
        window.parent.document.getElementById('edit-modal').style.display = 'none';
    }
};

// Auto-initialize with multiple fallbacks
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', window.initEditForm);
} else {
    window.initEditForm();
}

// Also try after a short delay to ensure DOM is ready
setTimeout(window.initEditForm, 500);