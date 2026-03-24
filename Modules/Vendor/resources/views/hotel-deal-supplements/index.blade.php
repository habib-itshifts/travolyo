<x-vendor::layouts.master title="Deal Supplements">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div><h5 class="fw-bold mb-0 text-dark">Deal Supplements</h5><p class="text-muted mb-0" style="font-size:13px;">Manage event-based price supplements</p></div>
        <a href="{{ route('vendor.hotel-deal-supplements.create') }}" class="btn btn-sm text-white d-inline-flex align-items-center gap-2" style="background:var(--clr-primary);border-radius:var(--radius-btn);">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Add Supplement
        </a>
    </div>
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('vendor.hotel-deal-supplements.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-5"><input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search event name..."></div>
                    <div class="col-md-4">
                        <select name="deal_id" class="form-select form-select-sm"><option value="">All Deals</option>
                            @foreach ($deals as $d)<option value="{{ $d->id }}" {{ request('deal_id')==$d->id ? 'selected' : '' }}>#{{ $d->id }} - {{ $d->hotel?->name }} / {{ $d->roomType?->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-dark px-3">Filter</button>
                        <a href="{{ route('vendor.hotel-deal-supplements.index') }}" class="btn btn-sm btn-outline-secondary px-3">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle" style="font-size:13px;">
                    <thead class="table-light border-bottom"><tr><th class="ps-4 py-3" style="width:40px;">#</th><th class="py-3">Event</th><th class="py-3">Deal</th><th class="py-3">Date Range</th><th class="py-3">Amount</th><th class="py-3 text-end pe-4">Actions</th></tr></thead>
                    <tbody>
                        @forelse ($supplements as $sup)
                            <tr>
                                <td class="ps-4 text-muted">{{ $sup->id }}</td>
                                <td class="fw-semibold">{{ $sup->event_name }}</td>
                                <td><p class="mb-0" style="font-size:12px;">{{ $sup->deal?->hotel?->name ?? '-' }}</p><p class="mb-0 text-muted" style="font-size:11px;">{{ $sup->deal?->roomType?->name ?? '-' }}</p></td>
                                <td style="font-size:12px;">{{ $sup->date_start->format('M d') }} - {{ $sup->date_end->format('M d, Y') }}</td>
                                <td class="fw-semibold">{{ number_format($sup->amount, 2) }}</td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="{{ route('vendor.hotel-deal-supplements.edit', $sup) }}" class="btn btn-sm btn-outline-primary"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                                        <form method="POST" action="{{ route('vendor.hotel-deal-supplements.destroy', $sup) }}" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-outline-danger"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-5">No supplements found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($supplements->hasPages()) <div class="px-4 py-3 border-top">{{ $supplements->links() }}</div> @endif
        </div>
    </div>
</x-vendor::layouts.master>
