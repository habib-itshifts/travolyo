<x-admin::layouts.master title="Edit Promo Code">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div><h5 class="fw-bold mb-0 text-dark">Edit Promo Code</h5><p class="text-muted mb-0" style="font-size:13px;">{{ $promoCode->code }}</p></div>
        <a href="{{ route('admin.promo-codes.index') }}" class="btn btn-sm btn-outline-secondary">Back to List</a>
    </div>
    <form method="POST" action="{{ route('admin.promo-codes.update', $promoCode) }}">@csrf @method('PUT') @include('admin::promo-codes._form')
        <div class="mt-4"><button type="submit" class="btn text-white px-4" style="background:var(--clr-primary);border-radius:var(--radius-btn);">Update Promo Code</button></div>
    </form>
</x-admin::layouts.master>
