<x-vendor::layouts.master title="Edit Deal - {{ $hotel->name }}">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div><h5 class="fw-bold mb-0 text-dark">Edit Deal #{{ $deal->id }}</h5><p class="text-muted mb-0" style="font-size:13px;">{{ $hotel->name }} - {{ $deal->roomType?->name }}</p></div>
        <a href="{{ route('vendor.hotels.deals.index', $hotel) }}" class="btn btn-sm btn-outline-secondary">Back to Deals</a>
    </div>
    <form method="POST" action="{{ route('vendor.hotels.deals.update', [$hotel, $deal]) }}">@csrf @method('PUT') @include('vendor::hotel-deals._form', ['isVendor' => true])
        <div class="mt-4"><button type="submit" class="btn text-white px-4" style="background:var(--clr-primary);border-radius:var(--radius-btn);">Update Deal</button></div>
    </form>
</x-vendor::layouts.master>
