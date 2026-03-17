<x-vendor::layouts.master>
    <x-slot name="title">My Documents</x-slot>

    {{-- ── Page Header ── --}}
    <div class="mb-4">
        <h1 class="fw-bold text-dark mb-1" style="font-size:1.2rem;">Verification Documents</h1>
        <p class="text-muted small mb-0">Upload the required documents to get your vendor account verified.</p>
    </div>

    {{-- ── Flash Messages ── --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ── Status Banner ── --}}
    @php $vs = $user->vendor_status; @endphp
    @if($vs?->value === 'docs_submitted')
        <div class="alert rounded-3 mb-4 d-flex align-items-center gap-3"
             style="background:rgba(99,102,241,0.08);border:1px solid rgba(99,102,241,0.2);color:#4f46e5;">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <strong>Documents submitted for review.</strong>
                You will be notified once the admin reviews your documents.
            </div>
        </div>
    @elseif($vs?->value === 'verified')
        <div class="alert rounded-3 mb-4 d-flex align-items-center gap-3"
             style="background:rgba(22,163,74,0.08);border:1px solid rgba(22,163,74,0.2);color:#15803d;">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            <div>
                <strong>Your account is verified!</strong>
                All your documents have been approved.
            </div>
        </div>
    @elseif($vs?->value === 'approved')
        <div class="alert rounded-3 mb-4 d-flex align-items-center gap-3"
             style="background:rgba(14,165,233,0.08);border:1px solid rgba(14,165,233,0.2);color:#0369a1;">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <strong>Account approved!</strong>
                Please upload the required documents below and submit for verification.
            </div>
        </div>
    @endif

    {{-- ── Document Cards ── --}}
    <div class="row g-3 mb-4">
        @foreach($types as $type)
            @php $doc = $docs[$type->value] ?? null; @endphp
            <div class="col-12 col-md-6">
                <div class="card border rounded-4 h-100" style="border-color:rgba(0,0,0,0.07)!important;">
                    <div class="card-body p-4">

                        {{-- Header --}}
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width:40px;height:40px;background:rgba(10,31,56,0.07);">
                                    <svg width="18" height="18" fill="none" stroke="#0a1f38" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="fw-semibold text-dark mb-0" style="font-size:13px;">
                                        {{ $type->label() }}
                                        @if($type->isRequired())
                                            <span class="text-danger ms-1" title="Required">*</span>
                                        @endif
                                    </p>
                                    <p class="text-muted mb-0" style="font-size:11px;">
                                        {{ $type->isRequired() ? 'Required' : 'Optional' }}
                                    </p>
                                </div>
                            </div>

                            {{-- Status Badge --}}
                            @if($doc)
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
                                    {{ $doc->status?->label() ?? 'Pending' }}
                                </span>
                            @else
                                <span class="badge rounded-pill fw-semibold"
                                      style="font-size:10px;background:rgba(0,0,0,0.05);color:#6b7280;">
                                    Not Uploaded
                                </span>
                            @endif
                        </div>

                        {{-- Uploaded File Info --}}
                        @if($doc)
                            <div class="rounded-3 p-3 mb-3 d-flex align-items-center gap-3"
                                 style="background:rgba(0,0,0,0.03);border:1px solid rgba(0,0,0,0.06);">
                                <svg width="16" height="16" fill="none" stroke="#6b7280" viewBox="0 0 24 24" style="flex-shrink:0;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                </svg>
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-dark fw-semibold mb-0 text-truncate" style="font-size:12px;">
                                        {{ $doc->original_name }}
                                    </p>
                                    <p class="text-muted mb-0" style="font-size:10px;">
                                        {{ number_format($doc->file_size / 1024, 1) }} KB &bull;
                                        {{ $doc->created_at->format('d M Y') }}
                                    </p>
                                </div>
                                @if($doc->status?->value === 'pending' || $doc->status?->value === 'rejected')
                                    <form method="POST" action="{{ route('vendor.documents.destroy', $doc) }}"
                                          onsubmit="return confirm('Remove this document?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm p-1"
                                                style="background:rgba(239,68,68,0.1);border:none;border-radius:6px;">
                                            <svg width="14" height="14" fill="none" stroke="#dc2626" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>

                            {{-- Admin Note if rejected --}}
                            @if($doc->status?->value === 'rejected' && $doc->admin_note)
                                <div class="rounded-3 p-2 mb-3"
                                     style="background:rgba(239,68,68,0.06);border:1px solid rgba(239,68,68,0.15);">
                                    <p class="mb-0 text-danger" style="font-size:11px;">
                                        <strong>Admin note:</strong> {{ $doc->admin_note }}
                                    </p>
                                </div>
                            @endif
                        @endif

                        {{-- Upload Form (hide if approved) --}}
                        @if(!$doc || $doc->status?->value !== 'approved')
                            <form method="POST" action="{{ route('vendor.documents.store') }}"
                                  enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="type" value="{{ $type->value }}">
                                <div class="d-flex gap-2">
                                    <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png"
                                           class="form-control form-control-sm rounded-3"
                                           style="font-size:12px;" required>
                                    <button type="submit" class="btn btn-sm fw-semibold text-white flex-shrink-0"
                                            style="background:linear-gradient(135deg,#0f1f38,#1a3a5c);border:none;border-radius:8px;font-size:11px;padding:6px 14px;white-space:nowrap;">
                                        {{ $doc ? 'Replace' : 'Upload' }}
                                    </button>
                                </div>
                                <p class="text-muted mt-1 mb-0" style="font-size:10px;">PDF, JPG, PNG — max 5 MB</p>
                            </form>
                        @endif

                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ── Submit for Review ── --}}
    @if(!in_array($vs?->value, ['docs_submitted', 'verified']))
        @php
            $requiredTypes = array_filter($types, fn($t) => $t->isRequired());
            $allRequiredUploaded = collect($requiredTypes)->every(fn($t) => isset($docs[$t->value]));
        @endphp
        <div class="card border rounded-4" style="border-color:rgba(0,0,0,0.07)!important;">
            <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <p class="fw-semibold text-dark mb-1" style="font-size:13px;">Ready to submit?</p>
                    <p class="text-muted mb-0" style="font-size:12px;">
                        Upload all required documents (marked with <span class="text-danger">*</span>) then submit for admin review.
                    </p>
                </div>
                <form method="POST" action="{{ route('vendor.documents.submit') }}">
                    @csrf
                    <button type="submit"
                            @if(!$allRequiredUploaded) disabled @endif
                            class="btn fw-semibold text-white"
                            style="background:{{ $allRequiredUploaded ? 'linear-gradient(135deg,#15803d,#16a34a)' : 'rgba(0,0,0,0.15)' }};border:none;border-radius:10px;font-size:13px;padding:10px 24px;"
                            onclick="return {{ $allRequiredUploaded ? 'confirm(\'Submit all documents for admin review?\')' : 'false' }}">
                        Submit for Review
                    </button>
                </form>
            </div>
        </div>
    @endif

</x-vendor::layouts.master>