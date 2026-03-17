<x-admin::layouts.master title="Edit Activity">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" style="font-size:12px;">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.activities.index') }}" class="text-decoration-none">Activities</a></li>
                    <li class="breadcrumb-item active">{{ $activity->title }}</li>
                </ol>
            </nav>
            <h5 class="fw-bold mb-0 text-dark">Edit: {{ $activity->title }}</h5>
        </div>
        @if ($activity->status === 'publish' && $activity->is_active)
            <a href="{{ route('activities.show', $activity) }}" target="_blank" class="btn btn-sm btn-outline-secondary">View Activity</a>
        @endif
    </div>

    @include('admin::activities._form')
</x-admin::layouts.master>
