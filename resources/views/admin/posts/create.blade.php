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

                    <input type="text"
                           id="tag-input"
                           class="form-control"
                           placeholder="Add tag and press Enter">

                    <input type="hidden"
                           id="tags"
                           name="tags"
                           value="{{ old('tags') }}">

                    <small class="text-muted">Press Enter or comma to add tag</small>
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
  toolbar:'undo redo | blocks | bold italic | bullist numlist | link table | code fullscreen'
});
</script>

{{-- ================= TAG JS (FIXED) ================= --}}
<script>
let tags = [];

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

function loadTags() {
    const raw = document.getElementById('tags').value;
    if (!raw) return;
    tags = raw.split(',').map(t => t.trim()).filter(Boolean);
    renderTags();
}

document.getElementById('tag-input').addEventListener('keydown', e => {
    if (e.key === 'Enter' || e.key === ',') {
        e.preventDefault();
        const val = e.target.value.trim();
        if (val && !tags.includes(val)) {
            tags.push(val);
            renderTags();
            syncHidden();
        }
        e.target.value = '';
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
