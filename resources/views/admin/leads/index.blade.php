@extends('layouts.admin')

@section('content')

{{-- ===== PAGE HEADER ===== --}}
<div class="posts-page-header d-flex justify-content-between align-items-center">
    <h1 class="fs-4 fw-medium mb-0" style="color: #1d2327;">Leads</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.leads.export') }}" class="btn btn-outline-secondary">
            <i class="fas fa-download me-1"></i> Export CSV
        </a>
    </div>
</div>

{{-- ===== ALERTS ===== --}}
@if(session('success'))
<div class="alert alert-success d-flex align-items-center small mb-3">
    <i class="fas fa-check-circle me-2 text-success"></i>
    <div>{{ session('success') }}</div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger small mb-3">
    <i class="fas fa-exclamation-circle me-2"></i> {{ $errors->first() }}
</div>
@endif

{{-- ===== TABLE ===== --}}
<div class="table-responsive posts-table admin-content-box">
    <table class="table table-hover align-middle mb-0">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Page</th>
                <th>Submitted</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($leads as $lead)
            <tr class="post-row">
                <td class="fw-medium">
                    {{ $lead->name ?? '—' }}
                </td>
                <td>
                    @if($lead->email)
                        <a href="mailto:{{ $lead->email }}">{{ $lead->email }}</a>
                    @else
                        —
                    @endif
                </td>
                <td>
                    {{ $lead->phone ?? '—' }}
                </td>
                <td>
                    @if($lead->page)
                        <a href="{{ route('admin.pages.edit', $lead->page) }}" class="text-decoration-none">
                            {{ $lead->page->title }}
                        </a>
                    @else
                        <span class="text-muted">Unknown Page</span>
                    @endif
                </td>
                <td class="date-column">
                    <span class="text-muted">{{ $lead->created_at->format('M j, Y g:i A') }}</span>
                </td>
                <td class="text-center">
                    <button class="btn btn-sm btn-outline-secondary" onclick="showPayload({{ $lead->id }})">
                        <i class="fas fa-eye"></i> View Data
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6">
                    <div class="admin-empty">
                        <div class="admin-empty-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h6 class="admin-empty-title">No leads yet</h6>
                        <p class="admin-empty-description">Lead submissions will appear here once users submit forms on published pages.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ===== PAGINATION ===== --}}
@if($leads->hasPages())
<div class="mt-3 d-flex justify-content-between align-items-center">
    <div class="text-muted small">
        Showing {{ $leads->firstItem() }} to {{ $leads->lastItem() }} of {{ $leads->total() }} leads
    </div>
    <div>
        {{ $leads->links('pagination::bootstrap-5') }}
    </div>
</div>
@endif

@endsection

{{-- ===== PAYLOAD MODAL ===== --}}
<div class="modal fade" id="payloadModal" tabindex="-1" aria-labelledby="payloadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="payloadModalLabel">Lead Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <pre id="payloadContent" class="bg-light p-3 rounded" style="font-size: 12px; max-height: 400px; overflow-y: auto;"></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
async function showPayload(leadId) {
    try {
        // For now, we'll show a placeholder. In a real implementation,
        // you might fetch the payload via AJAX or store it differently
        const payload = {
            lead_id: leadId,
            note: "Payload viewing would be implemented here with proper data fetching"
        };

        document.getElementById('payloadContent').textContent = JSON.stringify(payload, null, 2);
        new bootstrap.Modal(document.getElementById('payloadModal')).show();
    } catch (error) {
        alert('Error loading lead data');
    }
}
</script>
@endpush
