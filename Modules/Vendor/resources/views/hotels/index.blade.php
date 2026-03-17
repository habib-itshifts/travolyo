<x-vendor::layouts.master title="My Hotels">

    {{-- Flash --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="font-size:13px;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="font-size:13px;">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h5 class="fw-bold mb-0 text-dark">My Hotels</h5>
            <p class="text-muted mb-0" style="font-size:13px;">Manage your hotel listings</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('vendor.hotels.scraping.create') }}" class="btn btn-sm btn-outline-dark d-inline-flex align-items-center gap-2">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5-6h3m-9 9h10a2 2 0 002-2V8a2 2 0 00-2-2H7a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                Scrape Hotel
            </a>
            <a href="{{ route('vendor.hotels.create') }}" class="btn btn-sm text-white d-inline-flex align-items-center gap-2"
               style="background:var(--clr-primary);border-radius:var(--radius-btn);">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add New Hotel
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('vendor.hotels.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-5">
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="form-control form-control-sm" placeholder="Search by name...">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All Status</option>
                            <option value="draft"     {{ request('status') === 'draft'     ? 'selected' : '' }}>Draft</option>
                            <option value="active"    {{ request('status') === 'active'    ? 'selected' : '' }}>Active</option>
                            <option value="inactive"  {{ request('status') === 'inactive'  ? 'selected' : '' }}>Inactive</option>
                            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-dark px-3">Filter</button>
                        <a href="{{ route('vendor.hotels.index') }}" class="btn btn-sm btn-outline-secondary px-3">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle" style="font-size:13px;">
                    <thead class="table-light border-bottom">
                        <tr>
                            <th class="ps-4 py-3" style="width:40px;">#</th>
                            <th class="py-3">Hotel</th>
                            <th class="py-3">Location</th>
                            <th class="py-3">Stars</th>
                            <th class="py-3">Rooms</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($hotels as $hotel)
                            <tr>
                                <td class="ps-4 text-muted">{{ $hotel->id }}</td>

                                <td>
                                    <a href="{{ route('vendor.hotels.show', $hotel->id) }}" class="d-flex align-items-center gap-2 text-decoration-none">
                                        <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0 text-white fw-bold"
                                             style="width:36px;height:36px;background:var(--clr-primary);font-size:12px;">
                                            {{ strtoupper(substr($hotel->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="mb-0 fw-semibold text-dark" style="line-height:1.2;" title="{{ $hotel->name }}">{{ Str::limit($hotel->name, 28) }}</p>
                                            <p class="mb-0 text-muted" style="font-size:11px;line-height:1.2;" title="{{ $hotel->short_description ?: $hotel->description }}">
                                                {{ Str::limit($hotel->short_description ?: $hotel->description ?: $hotel->slug, 30) }}
                                            </p>
                                            <p class="mb-0 text-muted" style="font-size:10px;line-height:1.2;" title="{{ $hotel->author?->name ?? 'Unknown' }}">
                                                Added by: {{ Str::limit($hotel->author?->name ?? 'Unknown', 18) }}
                                            </p>
                                        </div>
                                    </a>
                                </td>

                                <td>
                                    <p class="mb-0">{{ $hotel->city }}, {{ $hotel->country }}</p>
                                    <p class="mb-0 text-muted" style="font-size:11px;">{{ Str::limit($hotel->address, 35) }}</p>
                                </td>

                                <td>
                                    @if ($hotel->star_rating)
                                        <span class="text-warning">
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <= $hotel->star_rating)
                                                    &#9733;
                                                @else
                                                    <span class="text-muted">&#9733;</span>
                                                @endif
                                            @endfor
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>

                                <td>
                                    <a href="{{ route('vendor.hotel-rooms.index', ['hotel_id' => $hotel->id]) }}"
                                       class="badge bg-light text-dark border text-decoration-none">
                                        {{ $hotel->rooms_count ?? $hotel->rooms()->count() }} rooms
                                    </a>
                                </td>

                                <td>
                                    @php
                                        $statusClass = match($hotel->status) {
                                            'active'    => 'bg-success-subtle text-success',
                                            'draft'     => 'bg-secondary-subtle text-secondary',
                                            'inactive'  => 'bg-warning-subtle text-warning',
                                            'suspended' => 'bg-danger-subtle text-danger',
                                            default     => 'bg-light text-muted',
                                        };
                                    @endphp
                                    <span class="badge rounded-pill {{ $statusClass }} text-capitalize">
                                        {{ $hotel->status }}
                                    </span>
                                </td>

                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="{{ route('vendor.hotel-rooms.index', ['hotel_id' => $hotel->id]) }}"
                                           class="btn btn-sm btn-outline-dark" title="Manage Rooms">
                                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M5 7l1 12h12l1-12M8 7V5a1 1 0 011-1h6a1 1 0 011 1v2"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('vendor.hotels.show', $hotel->id) }}"
                                           class="btn btn-sm btn-outline-secondary" title="View">
                                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('vendor.hotels.edit', $hotel->id) }}"
                                           class="btn btn-sm btn-outline-primary" title="Edit">
                                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('vendor.hotels.destroy', $hotel->id) }}"
                                              onsubmit="return confirm('Delete {{ addslashes($hotel->name) }}?')">
                                            @csrf @method('DELETE')
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
                                <td colspan="7" class="text-center text-muted py-5">
                                    <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="mb-2 opacity-25">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <p class="mb-1">No hotels yet. Add your first hotel.</p>
                                    <div class="d-flex justify-content-center gap-2 mt-2">
                                        <a href="{{ route('vendor.hotels.scraping.create') }}" class="btn btn-sm btn-outline-dark">Scrape Hotel</a>
                                        <a href="{{ route('vendor.hotels.create') }}" class="btn btn-sm mt-0 text-white"
                                           style="background:var(--clr-primary);">Add New Hotel</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($hotels->hasPages())
                <div class="px-4 py-3 border-top">
                    {{ $hotels->links() }}
                </div>
            @endif
        </div>
    </div>

</x-vendor::layouts.master>
