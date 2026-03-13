<x-admin::layouts.master title="Hotel Rooms">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h5 class="fw-bold mb-0 text-dark">Hotel Rooms</h5>
            <p class="text-muted mb-0" style="font-size:13px;">Add, edit, delete and assign rooms to hotels.</p>
        </div>
        <a href="{{ route('admin.hotel-rooms.create', ['hotel_id' => request('hotel_id')]) }}" class="btn btn-sm text-white d-inline-flex align-items-center gap-2"
           style="background:var(--clr-primary);border-radius:var(--radius-btn);">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add New Room
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.hotel-rooms.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search by room name...">
                    </div>
                    <div class="col-md-4">
                        <select name="hotel_id" class="form-select form-select-sm">
                            <option value="">All Hotels</option>
                            @foreach ($hotels as $hotel)
                                <option value="{{ $hotel->id }}" {{ (string) request('hotel_id') === (string) $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-dark px-3">Filter</button>
                        <a href="{{ route('admin.hotel-rooms.index') }}" class="btn btn-sm btn-outline-secondary px-3">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle" style="font-size:13px;">
                    <thead class="table-light border-bottom">
                        <tr>
                            <th class="ps-4 py-3">#</th>
                            <th class="py-3">Room</th>
                            <th class="py-3">Hotel</th>
                            <th class="py-3">Type</th>
                            <th class="py-3">Occupancy</th>
                            <th class="py-3">Base Price</th>
                            <th class="py-3">Quantity</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rooms as $room)
                            <tr>
                                <td class="ps-4 text-muted">{{ $room->id }}</td>
                                <td>
                                    <div>
                                        <p class="mb-0 fw-semibold text-dark">{{ $room->name }}</p>
                                        <p class="mb-0 text-muted" style="font-size:11px;">{{ $room->slug }}</p>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('admin.hotel-rooms.index', ['hotel_id' => $room->hotel_id]) }}" class="text-decoration-none">
                                        {{ $room->hotel?->name }}
                                    </a>
                                </td>
                                <td class="text-capitalize">{{ $room->room_type }}</td>
                                <td>{{ $room->max_adults }}A / {{ $room->max_children }}C / {{ $room->max_occupancy }} Total</td>
                                <td>${{ number_format((float) $room->base_price, 2) }}</td>
                                <td>{{ $room->quantity }}</td>
                                <td>
                                    <span class="badge rounded-pill {{ $room->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                        {{ $room->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.hotel-rooms.edit', $room->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.hotel-rooms.destroy', $room->id) }}" onsubmit="return confirm('Delete {{ addslashes($room->name) }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
                                    No rooms found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($rooms->hasPages())
                <div class="px-4 py-3 border-top">
                    {{ $rooms->links() }}
                </div>
            @endif
        </div>
    </div>

</x-admin::layouts.master>
