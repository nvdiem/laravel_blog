@extends('layouts.admin')

{{-- Include Media Picker Modal --}}
@include('admin.media._media_picker')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="fs-3 fw-normal mb-0 font-monospace-system">Add New Post</h1>
</div>

<form id="post-form" action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="row g-3"> {{-- Reduced gutter --}}
        {{-- ================= MAIN CONTENT ================= --}}
        <div class="col-lg-9"> {{-- WP uses a wider main column usually --}}
            
            {{-- ALERTS --}}
            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show small">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
            </div>
            @endif

            {{-- TITLE --}}
            <div class="mb-3">
                <input type="text"
                       class="form-control form-control-lg rounded-0 border px-3"
                       name="title"
                       id="title"
                       value="{{ old('title') }}"
                       placeholder="Add title"
                       style="font-size: 1.5rem; height: 50px;"
                       required>
            </div>

            {{-- SLUG PREVIEW --}}
            @if(old('title'))
            <div class="mb-3 d-flex align-items-center bg-white border p-1 ps-2 rounded-1">
                <small class="text-muted me-1">Permalink:</small>
                <span class="text-muted small">{{ url('/posts') }}/</span>
                <input type="text"
                       class="border-0 bg-transparent p-0 text-dark small fw-bold"
                       id="slug-preview"
                       value="{{ Str::slug(old('title')) }}"
                       readonly>
            </div>
            @endif

            {{-- CONTENT --}}
            <div class="bg-white">
                <textarea name="content"
                          id="content"
                          rows="20"
                          class="form-control rounded-0"
                          placeholder="Start writing...">{{ old('content') }}</textarea>
            </div>
        </div>

        {{-- ================= SIDEBAR ================= --}}
        <div class="col-lg-3">

            {{-- PUBLISH META BOX --}}
            <div class="card mb-3">
                <div class="card-header">Publish</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small text-muted">Status:</label>
                        <select class="form-select form-select-sm" name="status" required>
                            <option value="draft" {{ old('status')=='draft'?'selected':'' }}>Draft</option>
                            <option value="review" {{ old('status')=='review'?'selected':'' }}>Pending Review</option>
                            @can('publish', \App\Models\Post::class)
                            <option value="approved" {{ old('status')=='approved'?'selected':'' }}>Approved</option>
                            <option value="published" {{ old('status')=='published'?'selected':'' }}>Published</option>
                            @endcan
                        </select>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                        <a href="#" class="text-danger small text-decoration-none">Move to Trash</a>
                        <button type="submit" class="btn btn-primary">Publish</button>
                    </div>
                </div>
            </div>

            {{-- CATEGORY META BOX --}}
            <div class="card mb-3">
                <div class="card-header">Categories</div>
                <div class="card-body p-0">
                    <div class="p-2 border-bottom bg-light">
                        <ul class="nav nav-tabs nav-fill small card-header-tabs" id="categoryTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active py-1" id="all-cats-tab" data-bs-toggle="tab" href="#all-cats" role="tab">All Categories</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-1" id="pop-cats-tab" data-bs-toggle="tab" href="#pop-cats" role="tab">Most Used</a>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="tab-content p-2" style="max-height: 200px; overflow-y: auto;">
                        <div class="tab-pane fade show active" id="all-cats" role="tabpanel">
                             @foreach($categories as $category)
                                <div class="form-check">
                                    <input class="form-check-input category-checkbox"
                                           type="checkbox"
                                           name="categories[]"
                                           value="{{ $category->id }}"
                                           id="category-{{ $category->id }}"
                                           {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="category-{{ $category->id }}">
                                        {{ $category->name }}
                                    </label>
                                </div>
                                {{-- Simple Child Handling Indentation --}}
                                @if($category->children && $category->children->count())
                                     @foreach($category->children as $child)
                                        <div class="form-check ms-3">
                                            <input class="form-check-input category-checkbox"
                                                   type="checkbox"
                                                   name="categories[]"
                                                   value="{{ $child->id }}"
                                                   id="category-{{ $child->id }}"
                                                   {{ in_array($child->id, old('categories', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="category-{{ $child->id }}">
                                                {{ $child->name }}
                                            </label>
                                        </div>
                                     @endforeach
                                @endif
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="p-2 border-top bg-light text-center">
                        <a href="{{ route('admin.categories.index') }}" target="_blank" class="small text-decoration-none">+ Add New Category</a>
                    </div>
                </div>
            </div>

            {{-- TAGS META BOX --}}
            <div class="card mb-3">
                <div class="card-header">Tags</div>
                <div class="card-body">
                    <div class="mb-2">
                        <input type="text" id="tag-input" class="form-control form-control-sm" placeholder="Add new tag" autocomplete="off">
                        <small class="text-muted d-block mt-1">Separate tags with commas</small>
                    </div>
                    <div id="tags-container" class="d-flex flex-wrap gap-1 mt-2"></div>
                    <input type="hidden" id="tags" name="tags" value="{{ old('tags') }}">
                    <div id="suggestions" class="position-absolute bg-white border shadow-sm rounded-1" style="display:none; z-index:1050; width: 90%;"></div>
                </div>
            </div>

            {{-- THUMBNAIL META BOX --}}
            <div class="card mb-3">
                <div class="card-header">Featured Image</div>
                <div class="card-body">
                    <div id="thumbnailPreview" class="mb-2 bg-light border d-flex align-items-center justify-content-center text-muted small" style="min-height: 150px; border-style: dashed !important;">
                        <span id="noImageText">No Image Selected</span>
                        <img id="thumbnailImage" src="" alt="Thumbnail" class="img-fluid d-none" style="max-height: 200px;">
                    </div>
                    
                    <input type="hidden" name="thumbnail" id="thumbnailInput" value="">
                    
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-primary" onclick="openThumbnailPicker()">
                            <i class="fas fa-images me-1"></i> Select from Media Library
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger d-none" id="removeThumbnailBtn" onclick="removeThumbnail()">
                            <i class="fas fa-times me-1"></i> Remove
                        </button>
                    </div>
                    <small class="text-muted d-block mt-2">
                        <i class="fas fa-info-circle"></i> Recommended: 1200x630px or larger
                    </small>
                </div>
            </div>

            
            
            {{-- SEO META BOX --}}
             <div class="card mb-3">
                <div class="card-header">SEO Settings</div>
                <div class="card-body">
                    <div class="mb-2">
                        <label class="form-label small text-muted">SEO Title</label>
                        <input type="text" class="form-control form-control-sm" name="seo_title" value="{{ old('seo_title') }}">
                    </div>
                     <div class="mb-2">
                        <label class="form-label small text-muted">Meta Description</label>
                        <textarea class="form-control form-control-sm" name="seo_description" rows="2">{{ old('seo_description') }}</textarea>
                    </div>
                </div>
            </div>

        </div>
    </div>
</form>

{{-- ================= TINYMCE ================= --}}
<script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
<script>
tinymce.init({
  selector:'#content',
  license_key:'gpl',
  height:600,
  menubar:true,
  plugins:'lists link code table fullscreen',
  toolbar:'undo redo | blocks | bold italic | bullist numlist | link table | media | code fullscreen',

  // Custom media button
  setup: function(editor) {
    editor.ui.registry.addButton('media', {
      text: 'Media Library',
      icon: 'image',
      tooltip: 'Insert from Media Library',
      onAction: function() {
        // Open media library modal
        const modal = new bootstrap.Modal(document.getElementById('mediaLibraryModal'));
        modal.show();
      }
    });
  }
});
</script>

{{-- ================= TAG JS WITH AUTOCOMPLETE ================= --}}
<script>
let tags = [];
let suggestions = [];
let selectedIndex = -1;

function syncHidden() {
    document.getElementById('tags').value = tags.join(',');
}

function renderTags() {
    const box = document.getElementById('tags-container');
    box.innerHTML = '';
    tags.forEach(tag => {
        const badge = document.createElement('span');
        badge.className = 'badge bg-secondary me-1 mb-1';
        badge.textContent = tag;

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'btn-close btn-close-white ms-1';
        btn.style.fontSize = '10px';
        btn.onclick = () => {
            tags = tags.filter(t => t !== tag);
            renderTags();
            syncHidden();
        };

        badge.appendChild(btn);
        box.appendChild(badge);
    });
}

function renderSuggestions() {
    const container = document.getElementById('suggestions');
    container.innerHTML = '';
    if (suggestions.length === 0) {
        container.style.display = 'none';
        return;
    }
    container.style.display = 'block';
    suggestions.forEach((suggestion, index) => {
        const item = document.createElement('div');
        item.className = `px-3 py-2 ${index === selectedIndex ? 'bg-primary text-white' : 'bg-white text-dark'}`;
        item.style.cursor = 'pointer';
        item.textContent = suggestion.name;
        item.onclick = () => selectSuggestion(suggestion.name);
        container.appendChild(item);
    });
}

function fetchSuggestions(query) {
    if (query.length < 1) {
        suggestions = [];
        renderSuggestions();
        return;
    }
    fetch(`{{ route('admin.tags.suggest') }}?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            suggestions = data.filter(tag => !tags.includes(tag.name));
            selectedIndex = -1;
            renderSuggestions();
        });
}

function selectSuggestion(tagName) {
    if (!tags.includes(tagName)) {
        tags.push(tagName);
        renderTags();
        syncHidden();
    }
    document.getElementById('tag-input').value = '';
    suggestions = [];
    renderSuggestions();
}

function loadTags() {
    const raw = document.getElementById('tags').value;
    if (!raw) return;
    tags = raw.split(',').map(t => t.trim()).filter(Boolean);
    renderTags();
}

const tagInput = document.getElementById('tag-input');
tagInput.addEventListener('input', e => {
    const query = e.target.value.trim();
    fetchSuggestions(query);
});

tagInput.addEventListener('keydown', e => {
    if (e.key === 'ArrowDown') {
        e.preventDefault();
        selectedIndex = Math.min(selectedIndex + 1, suggestions.length - 1);
        renderSuggestions();
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        selectedIndex = Math.max(selectedIndex - 1, -1);
        renderSuggestions();
    } else if (e.key === 'Enter') {
        if (selectedIndex >= 0 && selectedIndex < suggestions.length) {
            e.preventDefault();
            selectSuggestion(suggestions[selectedIndex].name);
        } else {
            const val = e.target.value.trim();
            if (val && !tags.includes(val)) {
                e.preventDefault();
                tags.push(val);
                renderTags();
                syncHidden();
                e.target.value = '';
                suggestions = [];
                renderSuggestions();
            }
        }
    } else if (e.key === ',') {
        e.preventDefault();
        const val = e.target.value.trim();
        if (val && !tags.includes(val)) {
            tags.push(val);
            renderTags();
            syncHidden();
            e.target.value = '';
            suggestions = [];
            renderSuggestions();
        }
    } else if (e.key === 'Escape') {
        suggestions = [];
        renderSuggestions();
    }
});

// Hide suggestions when clicking outside
document.addEventListener('click', e => {
    if (!tagInput.contains(e.target) && !document.getElementById('suggestions').contains(e.target)) {
        suggestions = [];
        renderSuggestions();
    }
});

loadTags();

// Category selection logic
function updatePrimaryRadios() {
    const selectedCategories = Array.from(document.querySelectorAll('.category-checkbox:checked')).map(cb => cb.value);
    const primaryRadios = document.querySelectorAll('.primary-radio');

    primaryRadios.forEach(radio => {
        const categoryId = radio.value;
        radio.disabled = !selectedCategories.includes(categoryId);
        radio.parentElement.style.opacity = selectedCategories.includes(categoryId) ? '1' : '0.5';
    });
}

// Initial update
updatePrimaryRadios();

// Listen for category checkbox changes
document.querySelectorAll('.category-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updatePrimaryRadios);
});
</script>

<script>
document.getElementById('title').addEventListener('input', function() {
    const slug = this.value.toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim();
    document.getElementById('slug-preview').value = slug;
});
</script>

{{-- Thumbnail Picker Functions --}}
<script>
function openThumbnailPicker() {
    openMediaPicker(function(mediaId, mediaUrl) {
        // Set thumbnail
        document.getElementById('thumbnailInput').value = mediaUrl.replace('{{ config('app.url') }}/storage/', '');
        document.getElementById('thumbnailImage').src = mediaUrl;
        document.getElementById('thumbnailImage').classList.remove('d-none');
        document.getElementById('noImageText').classList.add('d-none');
        document.getElementById('removeThumbnailBtn').classList.remove('d-none');
    });
}

function removeThumbnail() {
    document.getElementById('thumbnailInput').value = '';
    document.getElementById('thumbnailImage').src = '';
    document.getElementById('thumbnailImage').classList.add('d-none');
    document.getElementById('noImageText').classList.remove('d-none');
    document.getElementById('removeThumbnailBtn').classList.add('d-none');
}
</script>

@endsection
