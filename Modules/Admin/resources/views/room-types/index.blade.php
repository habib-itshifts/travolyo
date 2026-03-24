<x-admin::layouts.master title="Room Types">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h5 class="fw-bold mb-0 text-dark">Room Types</h5>
            <p class="text-muted mb-0" style="font-size:13px;">Manage room type templates</p>
        </div>
        <a href="{{ route('admin.room-types.create') }}" class="btn btn-sm text-white d-inline-flex align-items-center gap-2"
           style="background:var(--clr-primary);border-radius:var(--radius-btn);">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Room Type
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.room-types.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-6">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search by name...">
                    </div>
                    <div class="col-md-3">
                        <select name="is_active" class="form-select form-select-sm">
                            <option value="">All Status</option>
                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-dark px-3">Filter</button>
                        <a href="{{ route('admin.room-types.index') }}" class="btn btn-sm btn-outline-secondary px-3">Reset</a>
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
                            <th class="ps-4 py-3" style="width:40px;">#</th>
                            <th class="py-3">Name</th>
                            <th class="py-3">Capacity</th>
                            <th class="py-3">Size</th>
                            <th class="py-3">Extra Pricing</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roomTypes as $rt)
                            <tr>
                                <td class="ps-4 text-muted">{{ $rt->id }}</td>
                                <td>
                                    <p class="mb-0 fw-semibold text-dark">{{ $rt->name }}</p>
                                    <p class="mb-0 text-muted" style="font-size:11px;">{{ $rt->view_type ?? '-' }}</p>
                                </td>
                                <td><span class="text-muted" style="font-size:12px;">{{ $rt->max_adults }}A / {{ $rt->max_children }}C (max {{ $rt->max_occupancy }})</span></td>
                                <td>{{ $rt->size_sqm ? $rt->size_sqm . ' sqm' : '-' }}</td>
                                <td><span style="font-size:12px;">Adult: {{ number_format($rt->extra_adult_price, 2) }} | Child: {{ number_format($rt->extra_child_price, 2) }}</span></td>
                                <td>
                                    <span class="badge rounded-pill {{ $rt->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                        {{ $rt->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="{{ route('admin.room-types.edit', $rt) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.room-types.destroy', $rt) }}" onsubmit="return confirm('Delete this room type?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-5">No room types found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($roomTypes->hasPages())
                <div class="px-4 py-3 border-top">{{ $roomTypes->links() }}</div>
            @endif
        </div>
    </div>
</x-admin::layouts.master>
