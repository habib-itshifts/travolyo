<x-admin::layouts.master :title="$hotel->name">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" style="font-size:12px;">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.hotels.index') }}" class="text-decoration-none">Hotels</a></li>
                    <li class="breadcrumb-item active">{{ $hotel->name }}</li>
                </ol>
            </nav>
            <h5 class="fw-bold mb-0 text-dark">{{ $hotel->name }}</h5>
            <p class="text-muted mb-0" style="font-size:13px;">Hotel details overview</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.hotels.deals.index', $hotel->id) }}" class="btn btn-sm text-white" style="background:var(--clr-primary);">Manage Deals</a>
            <a href="{{ route('admin.hotels.edit', $hotel->id) }}" class="btn btn-sm btn-primary">Edit Hotel</a>
            <a href="{{ route('admin.hotels.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                @if ($hotel->banner_image_url)
                    <img src="{{ asset($hotel->banner_image_url) }}" alt="{{ $hotel->name }}" style="width:100%;height:280px;object-fit:cover;">
                @endif
                <div class="card-body p-4">
                    <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                        <div>
                            <h4 class="fw-bold mb-1">{{ $hotel->name }}</h4>
                            <p class="text-muted mb-0">{{ $hotel->address }}, {{ $hotel->city }}, {{ $hotel->country }}</p>
                        </div>
                        <div class="text-end">
                            <span class="badge rounded-pill bg-light text-dark border text-capitalize">{{ $hotel->status }}</span>
                            @if ($hotel->is_featured)
                                <div class="mt-2"><span class="badge rounded-pill bg-warning-subtle text-warning border">Featured</span></div>
                            @endif
                        </div>
                    </div>

                    @if ($hotel->short_description)
                        <p class="mb-3">{{ $hotel->short_description }}</p>
                    @endif

                    @if ($hotel->description)
                        <div class="text-muted" style="line-height:1.7;">{!! nl2br(e($hotel->description)) !!}</div>
                    @endif
                </div>
            </div>

            @if (!empty($hotel->gallery_urls))
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-semibold mb-3">Gallery</h6>
                        <div class="row g-3">
                            @foreach ($hotel->gallery_urls as $image)
                                <div class="col-md-4 col-6">
                                    <img src="{{ asset($image) }}" alt="Gallery Image" class="rounded-3 border w-100" style="height:140px;object-fit:cover;">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3">Amenities</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @forelse ($hotel->amenities as $amenity)
                            <span class="badge bg-light text-dark border px-3 py-2">{{ $amenity->name }}</span>
                        @empty
                            <span class="text-muted">No amenities selected.</span>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3">Services</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @forelse ($hotel->services as $service)
                            <span class="badge bg-light text-dark border px-3 py-2">{{ $service->name }}</span>
                        @empty
                            <span class="text-muted">No services selected.</span>
                        @endforelse
                    </div>
                </div>
            </div>

            @if (!empty($hotel->policies))
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-semibold mb-3">Policies</h6>
                        <div class="d-grid gap-3">
                            @foreach ($hotel->policies as $policy)
                                <div class="border rounded-3 p-3">
                                    <p class="fw-semibold mb-1">{{ $policy['title'] ?? 'Policy' }}</p>
                                    <p class="text-muted mb-0">{{ $policy['content'] ?? '' }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3">Quick Info</h6>
                    <div class="hotel-meta-list">
                        <div><span>Slug</span><strong>{{ $hotel->slug }}</strong></div>
                        <div><span>Stars</span><strong>{{ $hotel->star_rating ?: 'N/A' }}</strong></div>
                        <div><span>Base Price</span><strong>{{ $hotel->base_price ? '$' . number_format((float) $hotel->base_price, 2) : 'N/A' }}</strong></div>
                        <div><span>Sale Price</span><strong>{{ $hotel->sale_price ? '$' . number_format((float) $hotel->sale_price, 2) : 'N/A' }}</strong></div>
                        <div><span>Check-in</span><strong>{{ $hotel->check_in_time ?: 'N/A' }}</strong></div>
                        <div><span>Check-out</span><strong>{{ $hotel->check_out_time ?: 'N/A' }}</strong></div>
                        <div><span>Min Advance Days</span><strong>{{ $hotel->min_day_before_booking ?? 'N/A' }}</strong></div>
                        <div><span>Min Stay Days</span><strong>{{ $hotel->min_day_stays ?? 'N/A' }}</strong></div>
                        <div><span>Rooms</span><strong>{{ $hotel->rooms->count() }}</strong></div>
                        <div><span>Deals</span><strong>{{ $hotel->deals->count() }}</strong></div>
                    </div>
                </div>
            </div>

            @if ($hotel->featured_image_url)
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-semibold mb-3">Featured Image</h6>
                        <img src="{{ asset($hotel->featured_image_url) }}" alt="Featured Image" class="rounded-3 border w-100" style="height:220px;object-fit:cover;">
                    </div>
                </div>
            @endif

            @if (!empty($hotel->nearby_places))
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-semibold mb-3">Nearby Places</h6>
                        <div class="d-grid gap-3">
                            @foreach ($hotel->nearby_places as $place)
                                <div class="border rounded-3 p-3">
                                    <p class="fw-semibold mb-1">{{ $place['name'] ?? 'Place' }}</p>
                                    <p class="text-muted mb-1">{{ $place['content'] ?? '' }}</p>
                                    <small class="text-muted">{{ $place['value'] ?? '' }} {{ $place['type'] ?? '' }}</small>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('styles')
    <style>
        .hotel-meta-list {
            display: grid;
            gap: 12px;
        }
        .hotel-meta-list div {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            border-bottom: 1px solid #eef2f7;
            padding-bottom: 10px;
            font-size: 13px;
        }
        .hotel-meta-list div:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }
        .hotel-meta-list span {
            color: #64748b;
        }
        .hotel-meta-list strong {
            color: #0f172a;
            text-align: right;
        }
    </style>
    @endpush

</x-admin::layouts.master>
