<x-admin::layouts.master>
    <x-slot name="title">Review Documents — {{ $vendor->name }}</x-slot>

    {{-- ── Breadcrumb / Header ── --}}
    <div class="mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <a href="{{ route('admin.vendor-requests.index', ['status' => 'docs_submitted']) }}"
               class="text-muted small d-inline-flex align-items-center gap-1 mb-1 text-decoration-none">
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Vendor Requests
            </a>
            <h1 class="fw-bold text-dark mb-0" style="font-size:1.2rem;">Review Documents</h1>
        </div>

        {{-- Vendor Info --}}
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                 style="width:42px;height:42px;font-size:14px;background:linear-gradient(135deg,#0f1f38,#3ab5d4);flex-shrink:0;">
                {{ strtoupper(substr($vendor->first_name, 0, 1)) }}{{ strtoupper(substr($vendor->last_name ?? '', 0, 1)) }}
            </div>
            <div>
                <p class="fw-semibold text-dark mb-0" style="font-size:14px;">{{ $vendor->name }}</p>
                <p class="text-muted mb-0" style="font-size:12px;">{{ $vendor->email }}</p>
            </div>
            @php $vs = $vendor->vendor_status; @endphp
            <span class="badge rounded-pill fw-semibold ms-2"
                  style="font-size:11px;
                    background:{{ match($vs?->value) {
                        'docs_submitted' => 'rgba(99,102,241,0.12)',
                        'verified'       => 'rgba(22,163,74,0.12)',
                        default          => 'rgba(0,0,0,0.06)',
                    } }};
                    color:{{ match($vs?->value) {
                        'docs_submitted' => '#4f46e5',
                        'verified'       => '#15803d',
                        default          => '#6b7280',
                    } }};">
                {{ $vs?->label() ?? '—' }}
            </span>
        </div>
    </div>

    {{-- ── Flash Messages ── --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ── Document List ── --}}
    @if($docs->isEmpty())
        <div class="card border rounded-4 shadow-sm text-center py-5 text-muted" style="border-color:rgba(0,0,0,0.06)!important;">
            <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="mx-auto mb-3 opacity-25">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="mb-0 small">No documents uploaded yet.</p>
        </div>
    @else
        <div class="row g-3 mb-4">
            @foreach($docs as $doc)
                <div class="col-12 col-md-6">
                    <div class="card border rounded-4 h-100" style="border-color:rgba(0,0,0,0.07)!important;">
                        <div class="card-body p-4">

                            {{-- Doc Header --}}
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div>
                                    <p class="fw-semibold text-dark mb-0" style="font-size:13px;">
                                        {{ $doc->type?->label() ?? $doc->type }}
                                    </p>
                                    <p class="text-muted mb-0" style="font-size:11px;">
                                        {{ $doc->original_name }} &bull;
                                        {{ number_format($doc->file_size / 1024, 1) }} KB
                                    </p>
                                </div>
                                @php
                                    $statusVal = $doc->status?->value ?? 'pending';
                                    $bg = match($statusVal) {
                                        'approved' => 'rgba(22,163,74,0.1)',
                                        'rejected' => 'rgba(239,68,68,0.1)',
                                        default    => 'rgba(234,179,8,0.1)',
                                    };
                                    $color = match($statusVal) {
                                        'approved' => '#15803d',
                                        'rejected' => '#dc2626',
                                        default    => '#a16207',
                                    };
                                @endphp
                                <span class="badge rounded-pill fw-semibold"
                                      style="font-size:10px;background:{{ $bg }};color:{{ $color }};">
                                    {{ $doc->status?->label() ?? 'Under Review' }}
                                </span>
                            </div>

                            {{-- Download / View --}}
                            <div class="mb-3">
                                <a href="{{ route('admin.vendor-requests.documents.download', $doc) }}"
                                   target="_blank"
                                   class="btn btn-sm fw-semibold d-inline-flex align-items-center gap-2"
                                   style="background:rgba(10,31,56,0.07);color:#0f1f38;border:none;border-radius:7px;font-size:11px;padding:5px 14px;">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    View / Download
                                </a>
                            </div>

                            {{-- Admin Note --}}
                            @if($doc->admin_note)
                                <div class="rounded-3 p-2 mb-3"
                                     style="background:rgba(239,68,68,0.06);border:1px solid rgba(239,68,68,0.15);">
                                    <p class="mb-0 text-danger" style="font-size:11px;">
                                        <strong>Note:</strong> {{ $doc->admin_note }}
                                    </p>
                                </div>
                            @endif

                            {{-- Reviewed by --}}
                            @if($doc->reviewed_at)
                                <p class="text-muted mb-3" style="font-size:11px;">
                                    Reviewed {{ $doc->reviewed_at->diffForHumans() }}
                                    @if($doc->reviewer) by {{ $doc->reviewer->name }} @endif
                                </p>
                            @endif

                            {{-- Actions (only if not yet approved) --}}
                            @if($doc->status?->value !== 'approved')
                                <div class="d-flex gap-2 flex-wrap">
                                    <form method="POST" action="{{ route('admin.vendor-requests.documents.approve', $doc) }}">
                                        @csrf
                                        <button type="submit"
                                                class="btn btn-sm fw-semibold text-white"
                                                style="background:linear-gradient(135deg,#15803d,#16a34a);border:none;border-radius:7px;font-size:11px;padding:5px 14px;"
                                                onclick="return confirm('Approve this document?')">
                                            Approve
                                        </button>
                                    </form>

                                    <button type="button"
                                            class="btn btn-sm fw-semibold"
                                            style="background:rgba(239,68,68,0.1);color:#dc2626;border:none;border-radius:7px;font-size:11px;padding:5px 14px;"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#reject-form-{{ $doc->id }}">
                                        Reject
                                    </button>
                                </div>

                                <div class="collapse mt-3" id="reject-form-{{ $doc->id }}">
                                    <form method="POST" action="{{ route('admin.vendor-requests.documents.reject', $doc) }}">
                                        @csrf
                                        <div class="mb-2">
                                            <textarea name="note" rows="2" placeholder="Reason for rejection (optional)"
                                                      class="form-control form-control-sm rounded-3"
                                                      style="font-size:12px;resize:none;"></textarea>
                                        </div>
                                        <button type="submit"
                                                class="btn btn-sm fw-semibold text-white"
                                                style="background:linear-gradient(135deg,#dc2626,#ef4444);border:none;border-radius:7px;font-size:11px;padding:5px 14px;">
                                            Confirm Reject
                                        </button>
                                    </form>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ── Mark as Verified ── --}}
        @if($vendor->vendor_status?->value === 'docs_submitted')
            @php $allApproved = $docs->every(fn($d) => $d->status?->value === 'approved'); @endphp
            <div class="card border rounded-4" style="border-color:rgba(0,0,0,0.07)!important;">
                <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <p class="fw-semibold text-dark mb-1" style="font-size:13px;">Verify Vendor</p>
                        <p class="text-muted mb-0" style="font-size:12px;">
                            @if($allApproved)
                                All documents approved. You can now mark this vendor as verified.
                            @else
                                Approve all documents before verifying the vendor.
                            @endif
                        </p>
                    </div>
                    <form method="POST" action="{{ route('admin.vendor-requests.verify', $vendor) }}">
                        @csrf
                        <button type="submit"
                                @if(!$allApproved) disabled @endif
                                class="btn fw-semibold text-white"
                                style="background:{{ $allApproved ? 'linear-gradient(135deg,#0f1f38,#1a3a5c)' : 'rgba(0,0,0,0.15)' }};border:none;border-radius:10px;font-size:13px;padding:10px 24px;"
                                onclick="return confirm('Mark {{ addslashes($vendor->name) }} as a verified vendor?')">
                            Mark as Verified
                        </button>
                    </form>
                </div>
            </div>
        @endif
    @endif

</x-admin::layouts.master>