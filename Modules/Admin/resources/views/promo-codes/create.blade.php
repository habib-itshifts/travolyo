<x-admin::layouts.master title="Create Promo Code">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div><h5 class="fw-bold mb-0 text-dark">Create Promo Code</h5><p class="text-muted mb-0" style="font-size:13px;">Add a new promotional code</p></div>
        <a href="{{ route('admin.promo-codes.index') }}" class="btn btn-sm btn-outline-secondary">Back to List</a>
    </div>
    <form method="POST" action="{{ route('admin.promo-codes.store') }}">@csrf @include('admin::promo-codes._form')
        <div class="mt-4"><button type="submit" class="btn text-white px-4" style="background:var(--clr-primary);border-radius:var(--radius-btn);">Create Promo Code</button></div>
    </form>
</x-admin::layouts.master>
