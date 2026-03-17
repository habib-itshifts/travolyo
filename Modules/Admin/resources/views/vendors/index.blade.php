<x-admin::layouts.master>
    <x-slot name="title">Vendors</x-slot>

    {{-- ── Page Header ── --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="fw-bold text-dark mb-1" style="font-size:1.2rem;">Vendors</h1>
            <p class="text-muted small mb-0">Manage all registered vendors on the platform.</p>
        </div>
        <span class="badge rounded-pill fw-semibold px-3 py-2"
              style="background:rgba(14,165,233,0.12);color:#0369a1;font-size:12px;">
            {{ $counts['all'] }} Total
        </span>
    </div>

    {{-- ── Flash ── --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ── Search + Filter ── --}}
    <form method="GET" action="{{ route('admin.vendors.index') }}" class="d-flex gap-2 flex-wrap mb-4">
        <input type="text" name="search" value="{{ request('search') }}"
               class="form-control form-control-sm rounded-3"
               style="max-width:260px;font-size:13px;" placeholder="Search name, email, business…">
        <select name="status" class="form-select form-select-sm rounded-3" style="max-width:160px;font-size:13px;">
            <option value="">All Statuses</option>
            @foreach(\App\Enums\VendorStatusEnum::cases() as $status)
                <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                    {{ $status->label() }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-sm btn-dark rounded-3" style="font-size:13px;">Filter</button>
        @if(request()->hasAny(['search','status']))
            <a href="{{ route('admin.vendors.index') }}" class="btn btn-sm btn-outline-secondary rounded-3" style="font-size:13px;">Clear</a>
        @endif
    </form>

    {{-- ── Table ── --}}
    <div class="card border rounded-4 shadow-sm" style="border-color:rgba(0,0,0,0.06)!important;">
        <div class="card-body p-0">
            @if($vendors->isEmpty())
                <div class="text-center py-5 text-muted">
                    <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="mb-3 opacity-25">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                    </svg>
                    <p class="mb-0 small">No vendors found.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                        <thead>
                            <tr class="text-uppercase text-muted border-bottom" style="font-size:10px;letter-spacing:.05em;">
                                <th class="fw-semibold ps-4 py-3">Vendor</th>
                                <th class="fw-semibold py-3">Business</th>
                                <th class="fw-semibold py-3">Email</th>
                                <th class="fw-semibold py-3">Phone</th>
                                <th class="fw-semibold py-3">Bookings</th>
                                <th class="fw-semibold py-3">Status</th>
                                <th class="fw-semibold py-3 text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vendors as $vendor)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                                                 style="width:36px;height:36px;font-size:13px;background:linear-gradient(135deg,#0f1f38,#3ab5d4);">
                                                {{ strtoupper(substr($vendor->first_name ?? $vendor->name, 0, 1)) }}{{ strtoupper(substr($vendor->last_name ?? '', 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="fw-semibold text-dark mb-0">{{ $vendor->name }}</p>
                                                <p class="text-muted mb-0" style="font-size:11px;">Joined {{ $vendor->created_at->format('d M Y') }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-muted">{{ $vendor->business_name ?? '—' }}</td>
                                    <td class="text-muted">{{ $vendor->email }}</td>
                                    <td class="text-muted">{{ $vendor->phone ?? '—' }}</td>
                                    <td>
                                        <span class="fw-semibold text-dark">{{ $vendor->bookings_as_vendor_count }}</span>
                                    </td>
                                    <td>
                                        @php $st = $vendor->vendor_status; @endphp
                                        <span class="badge rounded-pill fw-semibold"
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
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('admin.vendors.show', $vendor) }}"
                                               class="btn btn-sm fw-semibold"
                                               style="background:rgba(14,165,233,0.1);color:#0369a1;border:none;border-radius:7px;font-size:11px;padding:4px 12px;">
                                                View
                                            </a>
                                            <a href="{{ route('admin.vendors.edit', $vendor) }}"
                                               class="btn btn-sm fw-semibold"
                                               style="background:rgba(99,102,241,0.1);color:#4f46e5;border:none;border-radius:7px;font-size:11px;padding:4px 12px;">
                                                Edit
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($vendors->hasPages())
                    <div class="px-4 py-3 border-top">
                        {{ $vendors->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

</x-admin::layouts.master>
