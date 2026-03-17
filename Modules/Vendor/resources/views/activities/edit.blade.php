<x-vendor::layouts.master title="Edit Activity">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" style="font-size:12px;">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('vendor.activities.index') }}" class="text-decoration-none">My Activities</a></li>
                    <li class="breadcrumb-item active">{{ $activity->title }}</li>
                </ol>
            </nav>
            <h5 class="fw-bold mb-0 text-dark">Edit: {{ $activity->title }}</h5>
            <p class="text-muted mb-0" style="font-size:13px;">Saving changes will reset status to <strong>Pending Review</strong>.</p>
        </div>
    </div>

    @include('vendor::activities._form')
</x-vendor::layouts.master>
