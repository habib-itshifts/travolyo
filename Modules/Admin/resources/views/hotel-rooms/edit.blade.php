<x-admin::layouts.master title="Edit Room">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" style="font-size:12px;">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.hotel-rooms.index', ['hotel_id' => $hotelRoom->hotel_id]) }}" class="text-decoration-none">Hotel Rooms</a></li>
                    <li class="breadcrumb-item active">{{ $hotelRoom->name }}</li>
                </ol>
            </nav>
            <h5 class="fw-bold mb-0 text-dark">Edit Room: {{ $hotelRoom->name }}</h5>
        </div>
    </div>

    @include('admin::hotel-rooms._form')
</x-admin::layouts.master>
