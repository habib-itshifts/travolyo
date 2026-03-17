<x-admin::layouts.master>
    <x-slot name="title">Customers</x-slot>

    {{-- ── Page Header ── --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="fw-bold text-dark mb-1" style="font-size:1.2rem;">Customers</h1>
            <p class="text-muted small mb-0">Manage all registered customers on the platform.</p>
        </div>
        <span class="badge rounded-pill fw-semibold px-3 py-2"
              style="background:rgba(22,163,74,0.12);color:#15803d;font-size:12px;">
            {{ $total }} Total
        </span>
    </div>

    {{-- ── Flash ── --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ── Search ── --}}
    <form method="GET" action="{{ route('admin.customers.index') }}" class="d-flex gap-2 flex-wrap mb-4">
        <input type="text" name="search" value="{{ request('search') }}"
               class="form-control form-control-sm rounded-3"
               style="max-width:280px;font-size:13px;" placeholder="Search name, email or phone…">
        <button type="submit" class="btn btn-sm btn-dark rounded-3" style="font-size:13px;">Search</button>
        @if(request()->filled('search'))
            <a href="{{ route('admin.customers.index') }}" class="btn btn-sm btn-outline-secondary rounded-3" style="font-size:13px;">Clear</a>
        @endif
    </form>

    {{-- ── Table ── --}}
    <div class="card border rounded-4 shadow-sm" style="border-color:rgba(0,0,0,0.06)!important;">
        <div class="card-body p-0">
            @if($customers->isEmpty())
                <div class="text-center py-5 text-muted">
                    <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="mb-3 opacity-25">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                    </svg>
                    <p class="mb-0 small">No customers found.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                        <thead>
                            <tr class="text-uppercase text-muted border-bottom" style="font-size:10px;letter-spacing:.05em;">
                                <th class="fw-semibold ps-4 py-3">Customer</th>
                                <th class="fw-semibold py-3">Email</th>
                                <th class="fw-semibold py-3">Phone</th>
                                <th class="fw-semibold py-3">Bookings</th>
                                <th class="fw-semibold py-3">Joined</th>
                                <th class="fw-semibold py-3 text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $customer)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                                                 style="width:36px;height:36px;font-size:13px;background:linear-gradient(135deg,#15803d,#22c55e);">
                                                {{ strtoupper(substr($customer->first_name ?? $customer->name, 0, 1)) }}{{ strtoupper(substr($customer->last_name ?? '', 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="fw-semibold text-dark mb-0">{{ $customer->name }}</p>
                                                @if($customer->city)
                                                    <p class="text-muted mb-0" style="font-size:11px;">{{ $customer->city }}{{ $customer->country ? ', ' . $customer->country : '' }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-muted">{{ $customer->email }}</td>
                                    <td class="text-muted">{{ $customer->phone ?? '—' }}</td>
                                    <td>
                                        <span class="fw-semibold text-dark">{{ $customer->bookings_as_customer_count }}</span>
                                    </td>
                                    <td class="text-muted">{{ $customer->created_at->format('d M Y') }}</td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('admin.customers.show', $customer) }}"
                                               class="btn btn-sm fw-semibold"
                                               style="background:rgba(22,163,74,0.1);color:#15803d;border:none;border-radius:7px;font-size:11px;padding:4px 12px;">
                                                View
                                            </a>
                                            <a href="{{ route('admin.customers.edit', $customer) }}"
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

                @if($customers->hasPages())
                    <div class="px-4 py-3 border-top">
                        {{ $customers->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

</x-admin::layouts.master>
