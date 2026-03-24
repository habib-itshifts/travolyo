<x-vendor::layouts.master title="Deals - {{ $hotel->name }}">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div><h5 class="fw-bold mb-0 text-dark">Deals for {{ $hotel->name }}</h5><p class="text-muted mb-0" style="font-size:13px;">Manage pricing deals and rates</p></div>
        <div class="d-flex gap-2">
            <a href="{{ route('vendor.hotels.show', $hotel) }}" class="btn btn-sm btn-outline-secondary">Back to Hotel</a>
            <a href="{{ route('vendor.hotels.deals.create', $hotel) }}" class="btn btn-sm text-white d-inline-flex align-items-center gap-2" style="background:var(--clr-primary);border-radius:var(--radius-btn);">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Add Deal
            </a>
        </div>
    </div>
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle" style="font-size:13px;">
                    <thead class="table-light border-bottom"><tr><th class="ps-4 py-3" style="width:40px;">#</th><th class="py-3">Room Type</th><th class="py-3">Policy</th><th class="py-3">Rates</th><th class="py-3">Status</th><th class="py-3 text-end pe-4">Actions</th></tr></thead>
                    <tbody>
                        @forelse ($deals as $deal)
                            <tr>
                                <td class="ps-4 text-muted">{{ $deal->id }}</td>
                                <td><p class="mb-0 fw-semibold">{{ $deal->roomType?->name ?? '-' }}</p><p class="mb-0 text-muted" style="font-size:11px;">{{ $deal->allocation ?? '-' }}</p></td>
                                <td><p class="mb-0" style="font-size:12px;">{{ $deal->cancellation_policy ?? '-' }}</p></td>
                                <td><span class="badge bg-light text-dark border">{{ $deal->rates->count() }} rates</span></td>
                                <td><span class="badge rounded-pill {{ $deal->status==='published' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} text-capitalize">{{ $deal->status }}</span></td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="{{ route('vendor.hotels.deals.edit', [$hotel, $deal]) }}" class="btn btn-sm btn-outline-primary"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                                        <form method="POST" action="{{ route('vendor.hotels.deals.destroy', [$hotel, $deal]) }}" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-outline-danger"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-5">No deals found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($deals->hasPages()) <div class="px-4 py-3 border-top">{{ $deals->links() }}</div> @endif
        </div>
    </div>
</x-vendor::layouts.master>
