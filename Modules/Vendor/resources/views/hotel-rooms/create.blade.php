<x-vendor::layouts.master title="Add New Room">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" style="font-size:12px;">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item">
                        <a href="{{ route('vendor.hotel-rooms.index', ['hotel_id' => request('hotel_id')]) }}" class="text-decoration-none">Hotel Rooms</a>
                    </li>
                    <li class="breadcrumb-item active">Add New</li>
                </ol>
            </nav>
            <h5 class="fw-bold mb-0 text-dark">Add New Room</h5>
        </div>
    </div>

    @include('vendor::hotel-rooms._form')
</x-vendor::layouts.master>
