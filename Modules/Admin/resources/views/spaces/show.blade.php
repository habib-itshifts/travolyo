<x-admin::layouts.master title="Space Details">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" style="font-size:12px;">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.spaces.index') }}" class="text-decoration-none">Spaces</a></li>
                    <li class="breadcrumb-item active">{{ $space->name }}</li>
                </ol>
            </nav>
            <h5 class="fw-bold mb-0 text-dark">{{ $space->name }}</h5>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.spaces.availability', $space->id) }}" class="btn btn-sm btn-outline-dark">Manage Availability</a>
            <a href="{{ route('admin.spaces.edit', $space->id) }}" class="btn btn-sm text-white" style="background:var(--clr-primary);">Edit Space</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-3 text-muted">Type</div>
                        <div class="col-sm-9"><span class="badge bg-light text-dark border text-capitalize">{{ $space->type }}</span></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 text-muted">Status</div>
                        <div class="col-sm-9">
                            @php
                                $statusClass = match($space->status) {
                                    'active' => 'bg-success-subtle text-success',
                                    'draft' => 'bg-secondary-subtle text-secondary',
                                    'inactive' => 'bg-warning-subtle text-warning',
                                    'suspended' => 'bg-danger-subtle text-danger',
                                    default => 'bg-light text-muted',
                                };
                            @endphp
                            <span class="badge rounded-pill {{ $statusClass }} text-capitalize">{{ $space->status }}</span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 text-muted">Location</div>
                        <div class="col-sm-9">{{ $space->address }}, {{ $space->city }}, {{ $space->country }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 text-muted">Capacity</div>
                        <div class="col-sm-9">{{ $space->max_guests }} guests &middot; {{ $space->bedrooms }} bedrooms &middot; {{ $space->bathrooms }} bathrooms &middot; {{ $space->beds }} beds</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 text-muted">Pricing</div>
                        <div class="col-sm-9">
                            {{ $space->currency }} {{ number_format($space->price_per_night, 2) }} / night
                            @if ($space->sale_price)
                                <span class="text-success ms-2">(Sale: {{ $space->currency }} {{ number_format($space->sale_price, 2) }})</span>
                            @endif
                            @if ($space->cleaning_fee)
                                <br><small class="text-muted">Cleaning fee: {{ $space->currency }} {{ number_format($space->cleaning_fee, 2) }}</small>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 text-muted">Check-in/out</div>
                        <div class="col-sm-9">{{ $space->check_in_time }} / {{ $space->check_out_time }}</div>
                    </div>
                    @if ($space->min_stay_nights > 1)
                    <div class="row mb-3">
                        <div class="col-sm-3 text-muted">Min Stay</div>
                        <div class="col-sm-9">{{ $space->min_stay_nights }} nights</div>
                    </div>
                    @endif
                    @if ($space->description)
                    <div class="row mb-3">
                        <div class="col-sm-3 text-muted">Description</div>
                        <div class="col-sm-9">{!! nl2br(e($space->description)) !!}</div>
                    </div>
                    @endif
                </div>
            </div>

            @if ($space->amenities->isNotEmpty())
            <div class="card border-0 shadow-sm rounded-3 mt-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="fw-bold mb-0">Amenities</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach ($space->amenities as $amenity)
                            <span class="badge bg-light text-dark border">{{ $amenity->name }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom">
                    <h6 class="fw-bold mb-0">Owner</h6>
                </div>
                <div class="card-body">
                    <p class="mb-1 fw-semibold">{{ $space->author?->name ?? 'N/A' }}</p>
                    <p class="mb-0 text-muted" style="font-size:12px;">{{ $space->author?->email ?? '' }}</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-3 mt-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="fw-bold mb-0">Quick Stats</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Bookings</span>
                        <span class="fw-semibold">{{ $space->spaceBookings->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Featured</span>
                        <span class="fw-semibold">{{ $space->is_featured ? 'Yes' : 'No' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Created</span>
                        <span class="fw-semibold">{{ $space->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin::layouts.master>
