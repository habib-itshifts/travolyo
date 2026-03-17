<x-admin::layouts.master>
    <x-slot name="title">Customer — {{ $customer->name }}</x-slot>

    {{-- ── Breadcrumb ── --}}
    <nav class="mb-4" style="font-size:13px;">
        <a href="{{ route('admin.customers.index') }}" class="text-muted text-decoration-none">Customers</a>
        <span class="text-muted mx-1">/</span>
        <span class="text-dark fw-semibold">{{ $customer->name }}</span>
    </nav>

    <div class="row g-4">

        {{-- ── Profile Card ── --}}
        <div class="col-lg-4">
            <div class="card border rounded-4 shadow-sm" style="border-color:rgba(0,0,0,0.06)!important;">
                <div class="card-body p-4 text-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold mx-auto mb-3"
                         style="width:64px;height:64px;font-size:22px;background:linear-gradient(135deg,#15803d,#22c55e);">
                        {{ strtoupper(substr($customer->first_name ?? $customer->name, 0, 1)) }}{{ strtoupper(substr($customer->last_name ?? '', 0, 1)) }}
                    </div>
                    <h5 class="fw-bold text-dark mb-1" style="font-size:15px;">{{ $customer->name }}</h5>
                    <p class="text-muted mb-3" style="font-size:12px;">Customer since {{ $customer->created_at->format('M Y') }}</p>

                    <div class="border-top pt-3 text-start">
                        <p class="text-muted mb-1" style="font-size:12px;"><strong>Email:</strong> {{ $customer->email }}</p>
                        <p class="text-muted mb-1" style="font-size:12px;"><strong>Phone:</strong> {{ $customer->phone ?? '—' }}</p>
                        <p class="text-muted mb-0" style="font-size:12px;"><strong>Joined:</strong> {{ $customer->created_at->format('d M Y') }}</p>
                    </div>

                    <a href="{{ route('admin.customers.edit', $customer) }}"
                       class="btn btn-sm fw-semibold text-white w-100 mt-3"
                       style="background:linear-gradient(135deg,#15803d,#16a34a);border:none;border-radius:8px;font-size:12px;">
                        Edit Customer
                    </a>
                </div>
            </div>

            {{-- Stats --}}
            <div class="card border rounded-4 shadow-sm mt-3" style="border-color:rgba(0,0,0,0.06)!important;">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-3" style="font-size:13px;">Overview</h6>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted" style="font-size:13px;">Total Bookings</span>
                        <span class="fw-bold text-dark">{{ $customer->bookings_as_customer_count }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted" style="font-size:13px;">Location</span>
                        <span class="fw-semibold text-dark" style="font-size:13px;">
                            {{ collect([$customer->city, $customer->country])->filter()->implode(', ') ?: '—' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Address ── --}}
        <div class="col-lg-8">
            <div class="card border rounded-4 shadow-sm" style="border-color:rgba(0,0,0,0.06)!important;">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-4" style="font-size:13px;">Address Details</h6>
                    <div class="row g-3" style="font-size:13px;">
                        <div class="col-sm-6">
                            <p class="text-muted mb-1" style="font-size:11px;">ADDRESS LINE 1</p>
                            <p class="fw-semibold text-dark mb-0">{{ $customer->address_line_1 ?: '—' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="text-muted mb-1" style="font-size:11px;">ADDRESS LINE 2</p>
                            <p class="fw-semibold text-dark mb-0">{{ $customer->address_line_2 ?: '—' }}</p>
                        </div>
                        <div class="col-sm-3">
                            <p class="text-muted mb-1" style="font-size:11px;">CITY</p>
                            <p class="fw-semibold text-dark mb-0">{{ $customer->city ?: '—' }}</p>
                        </div>
                        <div class="col-sm-3">
                            <p class="text-muted mb-1" style="font-size:11px;">STATE</p>
                            <p class="fw-semibold text-dark mb-0">{{ $customer->state ?: '—' }}</p>
                        </div>
                        <div class="col-sm-3">
                            <p class="text-muted mb-1" style="font-size:11px;">COUNTRY</p>
                            <p class="fw-semibold text-dark mb-0">{{ $customer->country ?: '—' }}</p>
                        </div>
                        <div class="col-sm-3">
                            <p class="text-muted mb-1" style="font-size:11px;">ZIP CODE</p>
                            <p class="fw-semibold text-dark mb-0">{{ $customer->zip_code ?: '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</x-admin::layouts.master>
