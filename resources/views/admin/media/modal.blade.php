<!-- Media Library Modal -->
<div class="modal fade" id="mediaLibraryModal" tabindex="-1" aria-labelledby="mediaLibraryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mediaLibraryModalLabel">Media Library</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Upload Form -->
                <div class="mb-4">
                    <form id="uploadForm" enctype="multipart/form-data">
                        @csrf
                        <div class="input-group">
                            <input type="file" class="form-control" id="fileInput" name="file" accept="image/*" required>
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </div>
                    </form>
                </div>

                <!-- Media Grid -->
                <div id="mediaGrid" class="row g-3">
                    <!-- Images will be loaded here -->
                </div>

                <!-- Loading -->
                <div id="loading" class="text-center py-4" style="display: none;">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <!-- Pagination -->
                <div id="mediaPagination" class="d-flex justify-content-center mt-4"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// Global variables
let currentPage = 1;
let selectedImageUrl = null;

// Load media
function loadMedia(page = 1) {
    currentPage = page;
    document.getElementById('loading').style.display = 'block';

    fetch(`{{ route('admin.media.index') }}?page=${page}`)
        .then(response => response.json())
        .then(data => {
            renderMedia(data.data);
            renderPagination(data);
            document.getElementById('loading').style.display = 'none';
        })
        .catch(error => {
            console.error('Error loading media:', error);
            document.getElementById('loading').style.display = 'none';
        });
}

// Render media grid
function renderMedia(media) {
    const grid = document.getElementById('mediaGrid');
    grid.innerHTML = '';

    media.forEach(item => {
        const col = document.createElement('div');
        col.className = 'col-md-3 col-sm-6';

        col.innerHTML = `
            <div class="card h-100">
                <img src="${item.url}" class="card-img-top" style="height: 120px; object-fit: cover;" alt="${item.file_name}">
                <div class="card-body p-2">
                    <p class="card-text small mb-2 text-truncate" title="${item.file_name}">${item.file_name}</p>
                    <div class="btn-group btn-group-sm w-100">
                        <button class="btn btn-outline-primary btn-sm" onclick="selectImage('${item.url}')">Insert</button>
                        <button class="btn btn-outline-secondary btn-sm" onclick="copyUrl('${item.url}')">Copy URL</button>
                    </div>
                </div>
            </div>
        `;

        grid.appendChild(col);
    });
}

// Render pagination
function renderPagination(data) {
    const pagination = document.getElementById('mediaPagination');
    pagination.innerHTML = '';

    if (data.last_page <= 1) return;

    const nav = document.createElement('nav');
    nav.innerHTML = `
        <ul class="pagination pagination-sm">
            ${data.prev_page_url ? `<li class="page-item"><a class="page-link" href="#" onclick="loadMedia(${data.current_page - 1})">Previous</a></li>` : ''}
            ${Array.from({length: data.last_page}, (_, i) => i + 1).map(page => `
                <li class="page-item ${page === data.current_page ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="loadMedia(${page})">${page}</a>
                </li>
            `).join('')}
            ${data.next_page_url ? `<li class="page-item"><a class="page-link" href="#" onclick="loadMedia(${data.current_page + 1})">Next</a></li>` : ''}
        </ul>
    `;
    pagination.appendChild(nav);
}

// Select image
function selectImage(url) {
    selectedImageUrl = url;
    // Insert into TinyMCE
    if (window.tinymce && window.tinymce.activeEditor) {
        window.tinymce.activeEditor.insertContent(`<img src="${url}" alt="" />`);
    }
    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('mediaLibraryModal'));
    modal.hide();
}

// Copy URL
function copyUrl(url) {
    navigator.clipboard.writeText(url).then(() => {
        alert('URL copied to clipboard!');
    });
}

// Handle upload
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('{{ route('admin.media.upload') }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reset form
            document.getElementById('fileInput').value = '';
            // Reload media
            loadMedia(currentPage);
        } else {
            alert('Upload failed');
        }
    })
    .catch(error => {
        console.error('Upload error:', error);
        alert('Upload failed');
    });
});

// Load media when modal opens
document.getElementById('mediaLibraryModal').addEventListener('show.bs.modal', function() {
    loadMedia(1);
});
</script>
