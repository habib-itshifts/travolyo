<x-admin::layouts.master>
    <x-slot name="title">Vendor — {{ $vendor->name }}</x-slot>

    {{-- ── Breadcrumb ── --}}
    <nav class="mb-4" style="font-size:13px;">
        <a href="{{ route('admin.vendors.index') }}" class="text-muted text-decoration-none">Vendors</a>
        <span class="text-muted mx-1">/</span>
        <span class="text-dark fw-semibold">{{ $vendor->name }}</span>
    </nav>

    <div class="row g-4">

        {{-- ── Profile Card ── --}}
        <div class="col-lg-4">
            <div class="card border rounded-4 shadow-sm" style="border-color:rgba(0,0,0,0.06)!important;">
                <div class="card-body p-4 text-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold mx-auto mb-3"
                         style="width:64px;height:64px;font-size:22px;background:linear-gradient(135deg,#0f1f38,#3ab5d4);">
                        {{ strtoupper(substr($vendor->first_name ?? $vendor->name, 0, 1)) }}{{ strtoupper(substr($vendor->last_name ?? '', 0, 1)) }}
                    </div>
                    <h5 class="fw-bold text-dark mb-1" style="font-size:15px;">{{ $vendor->name }}</h5>
                    @if($vendor->business_name)
                        <p class="text-muted mb-2" style="font-size:13px;">{{ $vendor->business_name }}</p>
                    @endif

                    @php $st = $vendor->vendor_status; @endphp
                    <span class="badge rounded-pill fw-semibold mb-3"
                          style="font-size:11px;
                            background:{{ match($st?->value) {
                                'pending'       => 'rgba(234,179,8,0.12)',
                                'approved'      => 'rgba(14,165,233,0.12)',
                                'docs_submitted'=> 'rgba(99,102,241,0.12)',
                                'verified'      => 'rgba(22,163,74,0.12)',
                                'rejected'      => 'rgba(239,68,68,0.12)',
                                default         => 'rgba(0,0,0,0.06)',
                            } }};
                            color:{{ match($st?->value) {
                                'pending'       => '#a16207',
                                'approved'      => '#0369a1',
                                'docs_submitted'=> '#4f46e5',
                                'verified'      => '#15803d',
                                'rejected'      => '#dc2626',
                                default         => '#6b7280',
                            } }};">
                        {{ $st?->label() ?? '—' }}
                    </span>

                    <div class="border-top pt-3 mt-1 text-start">
                        <p class="text-muted mb-1" style="font-size:12px;"><strong>Email:</strong> {{ $vendor->email }}</p>
                        <p class="text-muted mb-1" style="font-size:12px;"><strong>Phone:</strong> {{ $vendor->phone ?? '—' }}</p>
                        @if($vendor->tax_number)
                            <p class="text-muted mb-1" style="font-size:12px;"><strong>Tax No:</strong> {{ $vendor->tax_number }}</p>
                        @endif
                        <p class="text-muted mb-0" style="font-size:12px;"><strong>Joined:</strong> {{ $vendor->created_at->format('d M Y') }}</p>
                    </div>

                    <a href="{{ route('admin.vendors.edit', $vendor) }}"
                       class="btn btn-sm fw-semibold text-white w-100 mt-3"
                       style="background:linear-gradient(135deg,#0f1f38,#1a3a5c);border:none;border-radius:8px;font-size:12px;">
                        Edit Vendor
                    </a>
                </div>
            </div>

            {{-- Stats --}}
            <div class="card border rounded-4 shadow-sm mt-3" style="border-color:rgba(0,0,0,0.06)!important;">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-3" style="font-size:13px;">Overview</h6>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted" style="font-size:13px;">Total Bookings</span>
                        <span class="fw-bold text-dark">{{ $vendor->bookings_as_vendor_count }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted" style="font-size:13px;">Address</span>
                        <span class="fw-semibold text-dark" style="font-size:13px;">
                            {{ collect([$vendor->city, $vendor->country])->filter()->implode(', ') ?: '—' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Details / Address ── --}}
        <div class="col-lg-8">
            <div class="card border rounded-4 shadow-sm" style="border-color:rgba(0,0,0,0.06)!important;">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-4" style="font-size:13px;">Address Details</h6>
                    <div class="row g-3" style="font-size:13px;">
                        <div class="col-sm-6">
                            <p class="text-muted mb-1" style="font-size:11px;">ADDRESS LINE 1</p>
                            <p class="fw-semibold text-dark mb-0">{{ $vendor->address_line_1 ?: '—' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="text-muted mb-1" style="font-size:11px;">ADDRESS LINE 2</p>
                            <p class="fw-semibold text-dark mb-0">{{ $vendor->address_line_2 ?: '—' }}</p>
                        </div>
                        <div class="col-sm-3">
                            <p class="text-muted mb-1" style="font-size:11px;">CITY</p>
                            <p class="fw-semibold text-dark mb-0">{{ $vendor->city ?: '—' }}</p>
                        </div>
                        <div class="col-sm-3">
                            <p class="text-muted mb-1" style="font-size:11px;">STATE</p>
                            <p class="fw-semibold text-dark mb-0">{{ $vendor->state ?: '—' }}</p>
                        </div>
                        <div class="col-sm-3">
                            <p class="text-muted mb-1" style="font-size:11px;">COUNTRY</p>
                            <p class="fw-semibold text-dark mb-0">{{ $vendor->country ?: '—' }}</p>
                        </div>
                        <div class="col-sm-3">
                            <p class="text-muted mb-1" style="font-size:11px;">ZIP CODE</p>
                            <p class="fw-semibold text-dark mb-0">{{ $vendor->zip_code ?: '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</x-admin::layouts.master>
