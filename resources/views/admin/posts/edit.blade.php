@extends('layouts.admin')

@section('content')
<form id="post-form"
      action="{{ route('admin.posts.update', $post) }}"
      method="POST"
      enctype="multipart/form-data">

@csrf
@method('PUT')

<div class="container-fluid">
    <div class="row g-4">

        {{-- ================= MAIN CONTENT ================= --}}
        <div class="col-lg-8">

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
                       class="form-control form-control-lg border-0 shadow-none"
                       name="title"
                       id="title"
                       value="{{ old('title', $post->title) }}"
                       placeholder="Add title"
                       style="font-size:2rem;font-weight:600;border-bottom:2px solid #dee2e6"
                       required>
            </div>

            {{-- SLUG --}}
            <div class="mb-4">
                <small class="text-muted">Permalink: /posts/</small>
                <input type="text"
                       class="border-0 bg-transparent p-0 text-muted small"
                       id="slug-preview"
                       value="{{ $post->slug }}"
                       readonly>
            </div>

            {{-- CONTENT --}}
            <textarea name="content"
                      id="content"
                      rows="20"
                      class="form-control border-0 shadow-none">
                {{ old('content', $post->content) }}
            </textarea>

            {{-- AUTOSAVE STATUS --}}
            @if($post->status === 'draft')
            <div id="autosave-status" class="mt-2 small text-muted" style="opacity: 0.7;">
                Draft saved locally
            </div>
            @endif
        </div>

        {{-- ================= SIDEBAR ================= --}}
        <div class="col-lg-4">

            {{-- PUBLISH --}}
            <div class="card mb-4">
                <div class="card-header bg-light fw-semibold">Publish</div>
                <div class="card-body">

                    <label class="form-label small">Status</label>
                    <select class="form-select form-select-sm mb-3" name="status">
                        <option value="draft" {{ old('status',$post->status)=='draft'?'selected':'' }}>Draft</option>
                        <option value="published" {{ old('status',$post->status)=='published'?'selected':'' }}>Published</option>
                    </select>

                    <button class="btn btn-primary w-100 mb-2">Update</button>
                    <a href="{{ \Illuminate\Support\Facades\URL::signedRoute('posts.preview', ['post' => $post]) }}"
                       target="_blank"
                       class="btn btn-outline-info btn-sm w-100 mb-2">
                        👁️ Preview Post
                    </a>
                    <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                        ← Back to Posts
                    </a>
                </div>
            </div>

            {{-- CATEGORY --}}
            <div class="card mb-4">
                <div class="card-header bg-light fw-semibold">Categories</div>
                <div class="card-body">
                    @if($errors->has('categories') || $errors->has('primary_category'))
                        <div class="alert alert-danger">
                            @if($errors->has('categories'))
                                <div>{{ $errors->first('categories') }}</div>
                            @endif
                            @if($errors->has('primary_category'))
                                <div>{{ $errors->first('primary_category') }}</div>
                            @endif
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Select Categories</label>
                        <div class="row">
                            @foreach($categories as $category)
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input category-checkbox"
                                               type="checkbox"
                                               name="categories[]"
                                               value="{{ $category->id }}"
                                               id="category-{{ $category->id }}"
                                               {{ in_array($category->id, old('categories', $post->categories->pluck('id')->toArray())) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="category-{{ $category->id }}">
                                            {{ $category->name }}
                                            @if($category->parent)
                                                <small class="text-muted">(Child of {{ $category->parent->name }})</small>
                                            @endif
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <small class="text-muted">Select one or more categories for this post.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Primary Category</label>
                        <div class="row">
                            @foreach($categories as $category)
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input primary-radio"
                                               type="radio"
                                               name="primary_category"
                                               value="{{ $category->id }}"
                                               id="primary-{{ $category->id }}"
                                               {{ old('primary_category', $post->primaryCategory()?->id) == $category->id ? 'checked' : '' }}>
                                        <label class="form-check-label" for="primary-{{ $category->id }}">
                                            {{ $category->name }}
                                            @if($category->parent)
                                                <small class="text-muted">(Child of {{ $category->parent->name }})</small>
                                            @endif
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <small class="text-muted">Select the primary category (must be one of the selected categories above).</small>
                    </div>
                </div>
            </div>

            {{-- TAGS --}}
            <div class="card mb-4">
                <div class="card-header bg-light fw-semibold">Tags</div>
                <div class="card-body">
                    <div id="tags-container" class="mb-2"></div>

                    <div class="position-relative">
                        <input type="text"
                               id="tag-input"
                               class="form-control"
                               placeholder="Add tag and press Enter"
                               autocomplete="off">
                        <div id="suggestions" class="position-absolute w-100 bg-white border rounded shadow-sm" style="display: none; z-index: 1000; max-height: 200px; overflow-y: auto;"></div>
                    </div>

                    <input type="hidden"
                           id="tags"
                           name="tags"
                           value="{{ old('tags', $post->tags->pluck('name')->implode(',')) }}">

                    <small class="text-muted">Press Enter or comma to add tag, or select from suggestions</small>
                </div>
            </div>

            {{-- THUMBNAIL --}}
            <div class="card mb-4">
                <div class="card-header bg-light fw-semibold">Featured Image</div>
                <div class="card-body">
                    @if($post->thumbnail)
                        <img src="{{ asset('storage/'.$post->thumbnail) }}"
                             class="img-fluid rounded border mb-2">
                    @endif
                    <input type="file" name="thumbnail" class="form-control form-control-sm">
                </div>
            </div>

            {{-- SEO --}}
            <div class="card mb-4">
                <div class="card-header bg-light fw-semibold">SEO</div>
                <div class="card-body">
                    <input type="text"
                           class="form-control form-control-sm mb-2"
                           name="seo_title"
                           placeholder="SEO Title"
                           value="{{ old('seo_title',$post->seo_title) }}">
                    <textarea class="form-control form-control-sm"
                              name="seo_description"
                              rows="3"
                              placeholder="Meta description">{{ old('seo_description',$post->seo_description) }}</textarea>
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

// ================= AUTOSAVE FUNCTIONALITY =================
@if($post->status === 'draft')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const AUTOSAVE_INTERVAL = 30000; // 30 seconds
    let autosaveTimer = null;
    let isDirty = false;
    let isSaving = false;
    let lastSavedData = null;

    const statusElement = document.getElementById('autosave-status');
    const postForm = document.getElementById('post-form');

    // Get initial form data for comparison
    function getFormData() {
        const formData = new FormData();

        // Get title
        formData.append('title', document.getElementById('title').value);

        // Get content from TinyMCE
        if (window.tinymce && window.tinymce.activeEditor) {
            formData.append('content', window.tinymce.activeEditor.getContent());
        } else {
            formData.append('content', document.getElementById('content').value);
        }

        // Get categories
        const selectedCategories = Array.from(document.querySelectorAll('.category-checkbox:checked')).map(cb => cb.value);
        selectedCategories.forEach(catId => {
            formData.append('categories[]', catId);
        });

        // Get primary category
        const primaryCategory = document.querySelector('.primary-radio:checked');
        if (primaryCategory) {
            formData.append('primary_category', primaryCategory.value);
        }

        // Get tags
        formData.append('tags', document.getElementById('tags').value);

        return formData;
    }

    // Check if form data has changed
    function hasFormChanged() {
        const currentData = getFormData();

        // Convert FormData to string for comparison
        const currentString = Array.from(currentData.entries())
            .sort(([a], [b]) => a.localeCompare(b))
            .map(([key, value]) => `${key}:${value}`)
            .join('|');

        const lastString = lastSavedData;

        return currentString !== lastString;
    }

    // Update last saved data
    function updateLastSavedData() {
        lastSavedData = Array.from(getFormData().entries())
            .sort(([a], [b]) => a.localeCompare(b))
            .map(([key, value]) => `${key}:${value}`)
            .join('|');
    }

    // Perform autosave
    function performAutosave() {
        if (isSaving || !isDirty) return;

        isSaving = true;
        updateStatus('Saving draft...', 'text-warning');

        const formData = getFormData();

        fetch(`{{ route('admin.posts.autosave', $post) }}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                isDirty = false;
                updateLastSavedData();
                updateStatus(`Draft saved at ${data.timestamp}`, 'text-success');
            } else {
                updateStatus('Failed to save draft', 'text-danger');
                console.error('Autosave failed:', data.message);
            }
        })
        .catch(error => {
            updateStatus('Failed to save draft', 'text-danger');
            console.error('Autosave error:', error);
        })
        .finally(() => {
            isSaving = false;
        });
    }

    // Update status display
    function updateStatus(message, className) {
        if (statusElement) {
            statusElement.textContent = message;
            statusElement.className = `mt-2 small ${className}`;
        }
    }

    // Mark form as dirty
    function markDirty() {
        if (!isDirty) {
            isDirty = true;
            updateStatus('Draft has unsaved changes', 'text-warning');
        }

        // Reset autosave timer
        clearTimeout(autosaveTimer);
        autosaveTimer = setTimeout(performAutosave, AUTOSAVE_INTERVAL);
    }

    // Initialize last saved data
    updateLastSavedData();

    // Event listeners for form changes
    const titleInput = document.getElementById('title');
    const tagInput = document.getElementById('tag-input');

    if (titleInput) {
        titleInput.addEventListener('input', markDirty);
    }

    if (tagInput) {
        tagInput.addEventListener('input', markDirty);
    }

    // Category changes
    document.querySelectorAll('.category-checkbox, .primary-radio').forEach(element => {
        element.addEventListener('change', markDirty);
    });

    // TinyMCE content changes
    if (window.tinymce) {
        window.tinymce.on('init', function() {
            window.tinymce.activeEditor.on('change keyup', function() {
                markDirty();
            });
        });
    }

    // Prevent autosave during manual form submission
    if (postForm) {
        postForm.addEventListener('submit', function() {
            clearTimeout(autosaveTimer);
            isSaving = true; // Prevent any ongoing autosave
        });
    }

    // Initial status
    updateStatus('Draft saved locally', 'text-muted');
});
</script>
@endif
</script>
@endsection
