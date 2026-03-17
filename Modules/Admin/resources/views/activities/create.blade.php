<x-admin::layouts.master title="Add Activity">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" style="font-size:12px;">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.activities.index') }}" class="text-decoration-none">Activities</a></li>
                    <li class="breadcrumb-item active">Add Activity</li>
                </ol>
            </nav>
            <h5 class="fw-bold mb-0 text-dark">Add Activity</h5>
            <p class="text-muted mb-0">Create a new tour or activity.</p>
        </div>
        <a href="{{ route('admin.activities.index') }}" class="btn btn-sm btn-outline-secondary">Back to Activities</a>
    </div>

    @include('admin::activities._form')
</x-admin::layouts.master>
