<x-admin::layouts.master title="Create Supplement">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div><h5 class="fw-bold mb-0 text-dark">Create Supplement</h5><p class="text-muted mb-0" style="font-size:13px;">Add event-based price supplement</p></div>
        <a href="{{ route('admin.hotel-deal-supplements.index') }}" class="btn btn-sm btn-outline-secondary">Back to List</a>
    </div>
    <form method="POST" action="{{ route('admin.hotel-deal-supplements.store') }}">@csrf @include('admin::hotel-deal-supplements._form')
        <div class="mt-4"><button type="submit" class="btn text-white px-4" style="background:var(--clr-primary);border-radius:var(--radius-btn);">Create Supplement</button></div>
    </form>
</x-admin::layouts.master>
