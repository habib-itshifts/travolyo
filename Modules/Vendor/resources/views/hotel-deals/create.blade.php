<x-vendor::layouts.master title="Create Deal">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" style="font-size:12px;">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('vendor.hotels.index') }}" class="text-decoration-none">Hotels</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('vendor.hotels.deals.index', $hotel->id) }}" class="text-decoration-none">Deals</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </nav>
            <h5 class="fw-bold mb-0 text-dark">Create Deal — {{ $hotel->name }}</h5>
        </div>
        <a href="{{ route('vendor.hotels.deals.index', $hotel->id) }}" class="btn btn-sm btn-outline-secondary">Back</a>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('vendor.hotels.deals.store', $hotel->id) }}">
                @csrf
                @include('vendor::hotel-deals._form')
                <div class="mt-3">
                    <button type="submit" class="btn text-white" style="background:var(--clr-primary);">Submit Deal for Review</button>
                </div>
            </form>
        </div>
    </div>

</x-vendor::layouts.master>
