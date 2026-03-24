<x-admin::layouts.master :title="'Deals — ' . $hotel->name">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" style="font-size:12px;">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.hotels.index') }}" class="text-decoration-none">Hotels</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.hotels.show', $hotel->id) }}" class="text-decoration-none">{{ Str::limit($hotel->name, 25) }}</a></li>
                    <li class="breadcrumb-item active">Deals</li>
                </ol>
            </nav>
            <h5 class="fw-bold mb-0 text-dark">Hotel Deals</h5>
            <p class="text-muted mb-0" style="font-size:13px;">{{ $hotel->name }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.hotels.deals.create', $hotel->id) }}" class="btn btn-sm text-white d-inline-flex align-items-center gap-2"
               style="background:var(--clr-primary);border-radius:var(--radius-btn);">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Deal
            </a>
            <a href="{{ route('admin.hotels.show', $hotel->id) }}" class="btn btn-sm btn-outline-secondary">Back to Hotel</a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.hotels.deals.index', $hotel->id) }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All Status</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-dark px-3">Filter</button>
                        <a href="{{ route('admin.hotels.deals.index', $hotel->id) }}" class="btn btn-sm btn-outline-secondary px-3">Reset</a>
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
                            <th class="py-3">Room Type</th>
                            <th class="py-3">Cancellation</th>
                            <th class="py-3">Allocation</th>
                            <th class="py-3">Rates</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($deals as $deal)
                            <tr>
                                <td class="ps-4 text-muted">{{ $deal->id }}</td>
                                <td>
                                    <p class="mb-0 fw-semibold">{{ $deal->room_type }}</p>
                                    @if ($deal->room)
                                        <small class="text-muted">Room: {{ $deal->room->name }}</small>
                                    @endif
                                </td>
                                <td>{{ $deal->cancellation_policy ?: '—' }}</td>
                                <td>{{ $deal->allocation ?: '—' }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $deal->rates->count() }} rate(s)</span></td>
                                <td>
                                    <span class="badge rounded-pill {{ $deal->status === 'published' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} text-capitalize">
                                        {{ $deal->status }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="{{ route('admin.hotels.deals.edit', [$hotel->id, $deal->id]) }}"
                                           class="btn btn-sm btn-outline-primary" title="Edit">
                                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.hotels.deals.destroy', [$hotel->id, $deal->id]) }}"
                                              onsubmit="return confirm('Delete this deal?')">
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
                                    No deals found for this hotel.
                                    <div class="mt-2">
                                        <a href="{{ route('admin.hotels.deals.create', $hotel->id) }}" class="btn btn-sm"
                                           style="background:var(--clr-primary);color:#fff;">Add First Deal</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($deals->hasPages())
                <div class="px-4 py-3 border-top">{{ $deals->links() }}</div>
            @endif
        </div>
    </div>

</x-admin::layouts.master>
