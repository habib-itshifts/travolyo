<x-vendor::layouts.master title="Add Activity">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" style="font-size:12px;">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('vendor.activities.index') }}" class="text-decoration-none">My Activities</a></li>
                    <li class="breadcrumb-item active">Add Activity</li>
                </ol>
            </nav>
            <h5 class="fw-bold mb-0 text-dark">Add Activity</h5>
            <p class="text-muted mb-0" style="font-size:13px;">Your activity will be reviewed by admin before going live.</p>
        </div>
        <a href="{{ route('vendor.activities.index') }}" class="btn btn-sm btn-outline-secondary">Back to Activities</a>
    </div>

    @include('vendor::activities._form')
</x-vendor::layouts.master>
