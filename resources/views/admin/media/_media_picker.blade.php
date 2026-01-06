{{-- Media Picker Modal for selecting images --}}
<div class="modal fade" id="mediaPicker" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Image from Media Library</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Upload Tab --}}
                <ul class="nav nav-tabs mb-3" id="mediaPickerTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="library-tab" data-bs-toggle="tab" data-bs-target="#library" type="button">
                            <i class="fas fa-images me-1"></i> Media Library
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload" type="button">
                            <i class="fas fa-upload me-1"></i> Upload New
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="mediaPickerTabContent">
                    {{-- Library Tab --}}
                    <div class="tab-pane fade show active" id="library" role="tabpanel">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <input type="search" id="mediaSearch" class="form-control form-control-sm" placeholder="Search media...">
                            </div>
                        </div>
                        <div id="mediaPickerGrid" class="media-picker-grid">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                        <div id="mediaPickerPagination" class="mt-3"></div>
                    </div>

                    {{-- Upload Tab --}}
                    <div class="tab-pane fade" id="upload" role="tabpanel">
                        <form id="quickUploadForm" enctype="multipart/form-data">
                            @csrf
                            <div class="upload-zone border-2 border-dashed rounded p-4 text-center" style="border-color: #2271b1; background: #f0f6fc;">
                                <i class="fas fa-cloud-upload fa-3x mb-3 text-primary"></i>
                                <p class="mb-2">Drag & drop files here or click to browse</p>
                                <input type="file" name="files[]" id="quickFileInput" multiple accept="image/*" class="d-none">
                                <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('quickFileInput').click()">
                                    <i class="fas fa-folder-open me-1"></i> Browse Files
                                </button>
                                <p class="text-muted small mt-3 mb-0">Max 5MB per file • JPG, PNG, GIF, WEBP</p>
                            </div>
                            <div id="quickFileList" class="mt-3"></div>
                            <button type="submit" class="btn btn-primary mt-3" id="quickUploadBtn" disabled>
                                <i class="fas fa-upload me-1"></i> Upload & Select
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="selectMediaBtn" disabled>Select Image</button>
            </div>
        </div>
    </div>
</div>

<style>
.media-picker-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 12px;
    max-height: 400px;
    overflow-y: auto;
}

.media-picker-item {
    border: 2px solid #ddd;
    border-radius: 4px;
    padding: 8px;
    cursor: pointer;
    transition: all 0.2s;
}

.media-picker-item:hover {
    border-color: #2271b1;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

.media-picker-item.selected {
    border-color: #2271b1;
    background: #f0f6fc;
}

.media-picker-item img {
    width: 100%;
    aspect-ratio: 4/3;
    object-fit: cover;
    border-radius: 2px;
}

.media-picker-item .filename {
    font-size: 11px;
    margin-top: 4px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
</style>

<script>
let selectedMediaId = null;
let selectedMediaUrl = null;
let currentPickerPage = 1;

// Open media picker
function openMediaPicker(callback) {
    window.mediaPickerCallback = callback;
    selectedMediaId = null;
    selectedMediaUrl = null;
    loadMediaForPicker(1);
    new bootstrap.Modal(document.getElementById('mediaPicker')).show();
}

// Load media for picker
function loadMediaForPicker(page = 1) {
    currentPickerPage = page;
    const search = document.getElementById('mediaSearch')?.value || '';
    
    document.getElementById('mediaPickerGrid').innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>';
    
    fetch(`{{ route('admin.media.index') }}?search=${search}&page=${page}`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(res => res.json())
    .then(data => {
        renderMediaPicker(data.data);
        renderPickerPagination(data);
    })
    .catch(err => {
        console.error('Error loading media:', err);
        document.getElementById('mediaPickerGrid').innerHTML = '<div class="alert alert-danger">Error loading media</div>';
    });
}

// Render media grid
function renderMediaPicker(media) {
    const grid = document.getElementById('mediaPickerGrid');
    
    if (!media || media.length === 0) {
        grid.innerHTML = '<div class="text-center text-muted py-4">No media files found</div>';
        return;
    }
    
    grid.innerHTML = media.map(item => `
        <div class="media-picker-item" data-id="${item.id}" data-url="${item.url}" onclick="selectMedia(${item.id}, '${item.url}')">
            <img src="${item.url}" alt="${item.file_name}" loading="lazy">
            <div class="filename text-muted" title="${item.file_name}">${item.file_name}</div>
        </div>
    `).join('');
}

// Render pagination
function renderPickerPagination(data) {
    const pagination = document.getElementById('mediaPickerPagination');
    
    if (data.last_page <= 1) {
        pagination.innerHTML = '';
        return;
    }
    
    let html = '<nav><ul class="pagination pagination-sm justify-content-center">';
    
    if (data.prev_page_url) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadMediaForPicker(${data.current_page - 1}); return false;">Previous</a></li>`;
    }
    
    for (let i = 1; i <= data.last_page; i++) {
        html += `<li class="page-item ${i === data.current_page ? 'active' : ''}"><a class="page-link" href="#" onclick="loadMediaForPicker(${i}); return false;">${i}</a></li>`;
    }
    
    if (data.next_page_url) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadMediaForPicker(${data.current_page + 1}); return false;">Next</a></li>`;
    }
    
    html += '</ul></nav>';
    pagination.innerHTML = html;
}

// Select media
function selectMedia(id, url) {
    selectedMediaId = id;
    selectedMediaUrl = url;
    
    // Remove previous selection
    document.querySelectorAll('.media-picker-item').forEach(item => {
        item.classList.remove('selected');
    });
    
    // Add selection to clicked item
    document.querySelector(`.media-picker-item[data-id="${id}"]`)?.classList.add('selected');
    
    // Enable select button
    document.getElementById('selectMediaBtn').disabled = false;
}

// Confirm selection
document.getElementById('selectMediaBtn')?.addEventListener('click', function() {
    if (selectedMediaUrl && window.mediaPickerCallback) {
        window.mediaPickerCallback(selectedMediaId, selectedMediaUrl);
        bootstrap.Modal.getInstance(document.getElementById('mediaPicker')).hide();
    }
});

// Search functionality
let searchTimeout;
document.getElementById('mediaSearch')?.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        loadMediaForPicker(1);
    }, 500);
});

// Quick upload
document.getElementById('quickFileInput')?.addEventListener('change', function(e) {
    const files = Array.from(e.target.files);
    document.getElementById('quickUploadBtn').disabled = files.length === 0;
    
    document.getElementById('quickFileList').innerHTML = files.map(f => `
        <div class="alert alert-light small mb-1">${f.name} (${(f.size / 1024).toFixed(2)} KB)</div>
    `).join('');
});

document.getElementById('quickUploadForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData();
    const files = document.getElementById('quickFileInput').files;
    
    Array.from(files).forEach(file => {
        formData.append('files[]', file);
    });
    formData.append('_token', '{{ csrf_token() }}');
    
    document.getElementById('quickUploadBtn').disabled = true;
    document.getElementById('quickUploadBtn').innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Uploading...';
    
    fetch('{{ route('admin.media.upload') }}', {
        method: 'POST',
        body: formData,
        headers: { 'Accept': 'application/json' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success && data.media && data.media.length > 0) {
            // Auto-select the first uploaded image
            const firstMedia = data.media[0];
            selectMedia(firstMedia.id, firstMedia.url);
            
            // Switch to library tab
            document.getElementById('library-tab').click();
            loadMediaForPicker(1);
            
            // Auto-confirm selection
            setTimeout(() => {
                document.getElementById('selectMediaBtn').click();
            }, 500);
        }
    })
    .catch(err => {
        alert('Upload failed');
        console.error(err);
    })
    .finally(() => {
        document.getElementById('quickUploadBtn').disabled = false;
        document.getElementById('quickUploadBtn').innerHTML = '<i class="fas fa-upload me-1"></i> Upload & Select';
    });
});
</script>
