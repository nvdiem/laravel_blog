@extends('layouts.admin')

@section('content')
<form id="post-form"
      action="{{ route('admin.posts.store') }}"
      method="POST"
      enctype="multipart/form-data">

@csrf

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
                       value="{{ old('title') }}"
                       placeholder="Add title"
                       style="font-size:2rem;font-weight:600;border-bottom:2px solid #dee2e6"
                       required>
            </div>

            {{-- SLUG PREVIEW --}}
            <div class="mb-4">
                <small class="text-muted">Permalink: /posts/</small>
                <input type="text"
                       class="border-0 bg-transparent p-0 text-muted small"
                       id="slug-preview"
                       value="{{ old('title') ? Str::slug(old('title')) : '' }}"
                       readonly>
            </div>

            {{-- CONTENT --}}
            <textarea name="content"
                      id="content"
                      rows="20"
                      class="form-control border-0 shadow-none"
                      placeholder="Start writing your post content...">{{ old('content') }}</textarea>
        </div>

        {{-- ================= SIDEBAR ================= --}}
        <div class="col-lg-4">

            {{-- PUBLISH --}}
            <div class="card mb-4">
                <div class="card-header bg-light fw-semibold">Publish</div>
                <div class="card-body">

                    <label class="form-label small">Status</label>
                    <select class="form-select form-select-sm mb-3" name="status" required>
                        <option value="draft" {{ old('status')=='draft'?'selected':'' }}>Draft</option>
                        <option value="published" {{ old('status')=='published'?'selected':'' }}>Published</option>
                    </select>

                    <button class="btn btn-primary w-100 mb-2">Publish</button>
                    <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                        ← Back to Posts
                    </a>
                </div>
            </div>

            {{-- CATEGORY --}}
            <div class="card mb-4">
                <div class="card-header bg-light fw-semibold">Category</div>
                <div class="card-body">
                    <select class="form-select" name="category_id">
                        <option value="">No category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ old('category_id')==$cat->id?'selected':'' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
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
                           value="{{ old('tags') }}">

                    <small class="text-muted">Press Enter or comma to add tag, or select from suggestions</small>
                </div>
            </div>

            {{-- THUMBNAIL --}}
            <div class="card mb-4">
                <div class="card-header bg-light fw-semibold">Featured Image</div>
                <div class="card-body">
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
                           value="{{ old('seo_title') }}">
                    <textarea class="form-control form-control-sm"
                              name="seo_description"
                              rows="3"
                              placeholder="Meta description">{{ old('seo_description') }}</textarea>
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
@endsection
