<x-admin::layouts.master title="Promo Codes">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h5 class="fw-bold mb-0 text-dark">Promo Codes</h5>
            <p class="text-muted mb-0" style="font-size:13px;">Manage promotional codes</p>
        </div>
        <a href="{{ route('admin.promo-codes.create') }}" class="btn btn-sm text-white d-inline-flex align-items-center gap-2"
           style="background:var(--clr-primary);border-radius:var(--radius-btn);">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Promo Code
        </a>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.promo-codes.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="form-control form-control-sm" placeholder="Search by code or label...">
                    </div>
                    <div class="col-md-3">
                        <select name="type" class="form-select form-select-sm">
                            <option value="">All Types</option>
                            @foreach (['online','offline','contracted','flash_sale'] as $t)
                                <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$t)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-dark px-3">Filter</button>
                        <a href="{{ route('admin.promo-codes.index') }}" class="btn btn-sm btn-outline-secondary px-3">Reset</a>
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
                            <th class="ps-4 py-3">#</th>
                            <th class="py-3">Code</th>
                            <th class="py-3">Label</th>
                            <th class="py-3">Type</th>
                            <th class="py-3">Discount</th>
                            <th class="py-3">Validity</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($promoCodes as $promo)
                            <tr>
                                <td class="ps-4 text-muted">{{ $promo->id }}</td>
                                <td><span class="badge bg-light text-dark border font-monospace">{{ $promo->code }}</span></td>
                                <td>{{ $promo->label ?: '—' }}</td>
                                <td><span class="badge bg-primary-subtle text-primary text-capitalize">{{ str_replace('_',' ',$promo->type) }}</span></td>
                                <td>
                                    @if ($promo->discount_type)
                                        {{ $promo->discount_value }}{{ $promo->discount_type === 'percent' ? '%' : ' (fixed)' }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($promo->valid_from || $promo->valid_until)
                                        {{ $promo->valid_from?->format('d M Y') ?? '...' }} — {{ $promo->valid_until?->format('d M Y') ?? '...' }}
                                    @else
                                        <span class="text-muted">No limit</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge rounded-pill {{ $promo->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                        {{ $promo->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="{{ route('admin.promo-codes.edit', $promo->id) }}"
                                           class="btn btn-sm btn-outline-primary" title="Edit">
                                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.promo-codes.destroy', $promo->id) }}"
                                              onsubmit="return confirm('Delete this promo code?')">
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
                                <td colspan="8" class="text-center text-muted py-5">No promo codes found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($promoCodes->hasPages())
                <div class="px-4 py-3 border-top">{{ $promoCodes->links() }}</div>
            @endif
        </div>
    </div>

</x-admin::layouts.master>
