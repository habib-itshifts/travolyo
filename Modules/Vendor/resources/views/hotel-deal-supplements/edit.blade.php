<x-vendor::layouts.master title="Edit Supplement">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" style="font-size:12px;">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('vendor.hotel-deal-supplements.index') }}" class="text-decoration-none">Supplements</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
            <h5 class="fw-bold mb-0 text-dark">Edit Supplement</h5>
        </div>
        <a href="{{ route('vendor.hotel-deal-supplements.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('vendor.hotel-deal-supplements.update', $supplement->id) }}">
                @csrf @method('PUT')
                @include('vendor::hotel-deal-supplements._form')
                <div class="mt-4">
                    <button type="submit" class="btn text-white" style="background:var(--clr-primary);">Update Supplement</button>
                </div>
            </form>
        </div>
    </div>

</x-vendor::layouts.master>
