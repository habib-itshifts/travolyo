<x-admin::layouts.master title="Add New Room">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" style="font-size:12px;">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.hotel-rooms.index') }}" class="text-decoration-none">Hotel Rooms</a></li>
                    <li class="breadcrumb-item active">Add New</li>
                </ol>
            </nav>
            <h5 class="fw-bold mb-0 text-dark">Add New Room</h5>
        </div>
    </div>

    @include('admin::hotel-rooms._form')
</x-admin::layouts.master>
