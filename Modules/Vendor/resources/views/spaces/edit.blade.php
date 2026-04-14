<x-vendor::layouts.master title="Edit Space">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" style="font-size:12px;">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('vendor.spaces.index') }}" class="text-decoration-none">Spaces</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
            <h5 class="fw-bold mb-0 text-dark">Edit Space: {{ $space->name }}</h5>
        </div>
    </div>
    @include('admin::spaces._form')
</x-vendor::layouts.master>
