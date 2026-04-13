<x-admin::layouts.master title="Add New Space">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" style="font-size:12px;">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.spaces.index') }}" class="text-decoration-none">Spaces</a></li>
                    <li class="breadcrumb-item active">Add New</li>
                </ol>
            </nav>
            <h5 class="fw-bold mb-0 text-dark">Add New Space</h5>
        </div>
    </div>
    @include('admin::spaces._form')
</x-admin::layouts.master>
