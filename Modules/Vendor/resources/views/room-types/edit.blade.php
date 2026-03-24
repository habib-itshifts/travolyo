<x-vendor::layouts.master title="Edit Room Type">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h5 class="fw-bold mb-0 text-dark">Edit Room Type</h5>
            <p class="text-muted mb-0" style="font-size:13px;">{{ $roomType->name }}</p>
        </div>
        <a href="{{ route('vendor.room-types.index') }}" class="btn btn-sm btn-outline-secondary">Back to List</a>
    </div>
    <form method="POST" action="{{ route('vendor.room-types.update', $roomType) }}">
        @csrf @method('PUT')
        @include('vendor::room-types._form')
        <div class="mt-4">
            <button type="submit" class="btn text-white px-4" style="background:var(--clr-primary);border-radius:var(--radius-btn);">Update Room Type</button>
        </div>
    </form>
</x-vendor::layouts.master>
