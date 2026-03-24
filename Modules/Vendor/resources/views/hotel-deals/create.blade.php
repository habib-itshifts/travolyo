<x-vendor::layouts.master title="Create Deal - {{ $hotel->name }}">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div><h5 class="fw-bold mb-0 text-dark">Create Deal</h5><p class="text-muted mb-0" style="font-size:13px;">{{ $hotel->name }}</p></div>
        <a href="{{ route('vendor.hotels.deals.index', $hotel) }}" class="btn btn-sm btn-outline-secondary">Back to Deals</a>
    </div>
    <form method="POST" action="{{ route('vendor.hotels.deals.store', $hotel) }}">@csrf @include('vendor::hotel-deals._form', ['isVendor' => true])
        <div class="mt-4"><button type="submit" class="btn text-white px-4" style="background:var(--clr-primary);border-radius:var(--radius-btn);">Create Deal</button></div>
    </form>
</x-vendor::layouts.master>
