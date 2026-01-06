{{-- Upload Modal using HTML5 dialog --}}
<dialog id="upload-modal" class="upload-modal">
    <div class="upload-modal-header">
        <h5 class="mb-0">Upload Files</h5>
        <button type="button" class="btn-close" onclick="closeUploadModal()"></button>
    </div>
    
    <form method="POST" action="{{ route('admin.media.upload') }}" enctype="multipart/form-data" id="upload-form" class="upload-modal-body">
        @csrf
        
        {{-- HTML5 Drag & Drop Zone --}}
        <div id="drop-zone" class="drop-zone">
            <i class="fas fa-cloud-upload fa-3x mb-3 text-primary"></i>
            <p class="mb-2 fw-medium">Drag & drop files here</p>
            <p class="text-muted small mb-3">or</p>
            <input type="file" name="files[]" id="file-input" multiple accept="image/*" class="d-none">
            <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('file-input').click()">
                <i class="fas fa-folder-open me-1"></i> Browse Files
            </button>
            <p class="text-muted small mt-3 mb-0">
                Max {{ config('media.upload.max_size') / 1024 }}MB per file • JPG, PNG, GIF, WEBP
            </p>
        </div>

        {{-- Selected Files List --}}
        <div id="file-list" class="file-list mt-3"></div>

        <div class="upload-modal-footer d-flex justify-content-end gap-2 mt-3">
            <button type="button" class="btn btn-secondary" onclick="closeUploadModal()">Cancel</button>
            <button type="submit" class="btn btn-primary" id="upload-btn" disabled>
                <i class="fas fa-upload me-1"></i> Upload Files
            </button>
        </div>
    </form>
</dialog>

<style>
.upload-modal {
    max-width: 600px;
    width: 90vw;
    border: 1px solid #c3c4c7;
    border-radius: 8px;
    padding: 0;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
}

.upload-modal::backdrop {
    background: rgba(0, 0, 0, 0.5);
}

.upload-modal-header {
    padding: 16px 20px;
    border-bottom: 1px solid #dcdcde;
    display: flex;
    justify-content: between;
    align-items: center;
}

.upload-modal-body {
    padding: 20px;
}

.upload-modal-footer {
    padding: 16px 20px;
    border-top: 1px solid #dcdcde;
    margin: 0 !important;
}

.drop-zone {
    border: 2px dashed #2271b1;
    border-radius: 8px;
    padding: 40px 20px;
    text-align: center;
    background: #f0f6fc;
    transition: all 0.2s ease;
    cursor: pointer;
}

.drop-zone:hover {
    border-color: #135e96;
    background: #e7f3ff;
}

.drop-zone.dragover {
    border-color: #135e96;
    background: #e7f3ff;
    border-style: solid;
}

.file-list {
    max-height: 200px;
    overflow-y: auto;
}

.file-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 12px;
    border: 1px solid #dcdcde;
    border-radius: 4px;
    margin-bottom: 8px;
    background: #fff;
}

.file-item:last-child {
    margin-bottom: 0;
}

.file-item-name {
    font-size: 13px;
    flex: 1;
    margin-right: 12px;
}

.file-item-size {
    font-size: 12px;
    color: #646970;
    margin-right: 12px;
}
</style>

<script>
const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('file-input');
const fileList = document.getElementById('file-list');
const uploadBtn = document.getElementById('upload-btn');
const uploadForm = document.getElementById('upload-form');
let selectedFiles = [];

// Open modal
function openUploadModal() {
    document.getElementById('upload-modal').showModal();
    // Reset form
    selectedFiles = [];
    fileList.innerHTML = '';
    fileInput.value = '';
    uploadBtn.disabled = true;
}

// Close modal
function closeUploadModal() {
    document.getElementById('upload-modal').close();
}

// Drag & drop events
dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('dragover');
});

dropZone.addEventListener('dragleave', () => {
    dropZone.classList.remove('dragover');
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('dragover');
    handleFiles(e.dataTransfer.files);
});

// Click to select files
dropZone.addEventListener('click', (e) => {
    if (e.target === dropZone || e.target.closest('.drop-zone')) {
        fileInput.click();
    }
});

// File input change
fileInput.addEventListener('change', (e) => {
    handleFiles(e.target.files);
});

// Handle files
function handleFiles(files) {
    selectedFiles = Array.from(files);
    displayFiles();
    uploadBtn.disabled = selectedFiles.length === 0;
}

// Display file list
function displayFiles() {
    fileList.innerHTML = selectedFiles.map((file, index) => `
        <div class="file-item">
            <div class="file-item-name">
                <i class="fas fa-file-image text-muted me-2"></i>
                ${file.name}
            </div>
            <div class="file-item-size">${(file.size / 1024).toFixed(2)} KB</div>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile(${index})">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `).join('');
}

// Remove file
function removeFile(index) {
    selectedFiles.splice(index, 1);
    displayFiles();
    uploadBtn.disabled = selectedFiles.length === 0;
    
    // Update file input
    const dt = new DataTransfer();
    selectedFiles.forEach(file => dt.items.add(file));
    fileInput.files = dt.files;
}

// Form submission
uploadForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Disable button during upload
    uploadBtn.disabled = true;
    uploadBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Uploading...';
    
    const formData = new FormData();
    selectedFiles.forEach(file => {
        formData.append('files[]', file);
    });
    
    formData.append('_token', document.querySelector('meta[name=csrf-token]').content);
    
    fetch('{{ route('admin.media.upload') }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            // Handle HTTP errors (4xx, 5xx)
            return response.json().then(data => {
                throw new Error(data.message || 'Upload failed');
            }).catch(() => {
                throw new Error('Upload failed. Server error.');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            closeUploadModal();
            location.reload(); // Reload to show new files
        } else {
            alert(data.message || 'Upload failed. Please try again.');
            uploadBtn.disabled = false;
            uploadBtn.innerHTML = '<i class="fas fa-upload me-1"></i> Upload Files';
        }
    })
    .catch(error => {
        console.error('Upload error:', error);
        alert(error.message || 'Upload failed. Please try again.');
        uploadBtn.disabled = false;
        uploadBtn.innerHTML = '<i class="fas fa-upload me-1"></i> Upload Files';
    });
});

// Close on Escape
document.getElementById('upload-modal').addEventListener('close', () => {
    selectedFiles = [];
    fileList.innerHTML = '';
    fileInput.value = '';
});
</script>
