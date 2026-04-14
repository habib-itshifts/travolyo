<x-vendor::layouts.master title="My Spaces">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h5 class="fw-bold mb-0 text-dark">My Spaces</h5>
            <p class="text-muted mb-0" style="font-size:13px;">Manage your space listings</p>
        </div>
        <a href="{{ route('vendor.spaces.create') }}" class="btn btn-sm text-white d-inline-flex align-items-center gap-2"
           style="background:var(--clr-primary);border-radius:var(--radius-btn);">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add New Space
        </a>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('vendor.spaces.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="form-control form-control-sm" placeholder="Search by name...">
                    </div>
                    <div class="col-md-3">
                        <select name="type" class="form-select form-select-sm">
                            <option value="">All Types</option>
                            <option value="apartment" {{ request('type') === 'apartment' ? 'selected' : '' }}>Apartment</option>
                            <option value="room" {{ request('type') === 'room' ? 'selected' : '' }}>Room</option>
                            <option value="studio" {{ request('type') === 'studio' ? 'selected' : '' }}>Studio</option>
                            <option value="villa" {{ request('type') === 'villa' ? 'selected' : '' }}>Villa</option>
                            <option value="house" {{ request('type') === 'house' ? 'selected' : '' }}>House</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All Status</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-dark px-3">Filter</button>
                        <a href="{{ route('vendor.spaces.index') }}" class="btn btn-sm btn-outline-secondary px-3">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive" style="overflow:visible;">
                <table class="table table-hover mb-0 align-middle" style="font-size:13px;">
                    <thead class="table-light border-bottom">
                        <tr>
                            <th class="ps-4 py-3" style="width:40px;">#</th>
                            <th class="py-3">Space</th>
                            <th class="py-3">Type</th>
                            <th class="py-3">Location</th>
                            <th class="py-3">Price/Night</th>
                            <th class="py-3">Capacity</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($spaces as $space)
                            <tr>
                                <td class="ps-4 text-muted">{{ $space->id }}</td>
                                <td>
                                    <a href="{{ route('vendor.spaces.show', $space->id) }}" class="d-flex align-items-center gap-2 text-decoration-none">
                                        <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0 text-white fw-bold"
                                             style="width:36px;height:36px;background:var(--clr-primary);font-size:12px;">
                                            {{ strtoupper(substr($space->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="mb-0 fw-semibold text-dark" style="line-height:1.2;">{{ Str::limit($space->name, 28) }}</p>
                                            <p class="mb-0 text-muted" style="font-size:11px;">{{ Str::limit($space->short_description ?: $space->slug, 30) }}</p>
                                        </div>
                                    </a>
                                </td>
                                <td><span class="badge bg-light text-dark border text-capitalize">{{ $space->type }}</span></td>
                                <td>
                                    <p class="mb-0">{{ $space->city }}, {{ $space->country }}</p>
                                </td>
                                <td>
                                    @if ($space->sale_price && $space->sale_price < $space->price_per_night)
                                        <span class="text-decoration-line-through text-muted">{{ $space->currency }} {{ number_format($space->price_per_night, 2) }}</span><br>
                                        <span class="fw-semibold text-success">{{ $space->currency }} {{ number_format($space->sale_price, 2) }}</span>
                                    @else
                                        <span class="fw-semibold">{{ $space->currency }} {{ number_format($space->price_per_night, 2) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted" style="font-size:12px;">{{ $space->max_guests }}g / {{ $space->bedrooms }}br / {{ $space->bathrooms }}ba</span>
                                </td>
                                <td>
                                    @php
                                        $statusClass = match($space->status) {
                                            'active' => 'bg-success-subtle text-success',
                                            'draft' => 'bg-secondary-subtle text-secondary',
                                            'inactive' => 'bg-warning-subtle text-warning',
                                            'suspended' => 'bg-danger-subtle text-danger',
                                            default => 'bg-light text-muted',
                                        };
                                    @endphp
                                    <span class="badge rounded-pill {{ $statusClass }} text-capitalize">{{ $space->status }}</span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border-0" type="button" data-bs-toggle="dropdown">
                                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                <circle cx="8" cy="3" r="1.5"/><circle cx="8" cy="8" r="1.5"/><circle cx="8" cy="13" r="1.5"/>
                                            </svg>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end" style="font-size:13px;">
                                            <li><a class="dropdown-item" href="{{ route('vendor.spaces.availability', $space->id) }}">Manage Availability</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item" href="{{ route('vendor.spaces.edit', $space->id) }}">Edit</a></li>
                                            <li>
                                                <form method="POST" action="{{ route('vendor.spaces.destroy', $space->id) }}"
                                                      onsubmit="return confirm('Delete {{ addslashes($space->name) }}?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">Delete</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <p class="mb-0">No spaces yet.</p>
                                    <a href="{{ route('vendor.spaces.create') }}" class="btn btn-sm mt-2" style="background:var(--clr-primary);color:#fff;">Add Your First Space</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($spaces->hasPages())
                <div class="px-4 py-3 border-top">{{ $spaces->links() }}</div>
            @endif
        </div>
    </div>
</x-vendor::layouts.master>
