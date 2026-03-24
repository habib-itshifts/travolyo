<x-admin::layouts.master title="Edit Promo Code">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" style="font-size:12px;">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.promo-codes.index') }}" class="text-decoration-none">Promo Codes</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
            <h5 class="fw-bold mb-0 text-dark">Edit Promo Code</h5>
        </div>
        <a href="{{ route('admin.promo-codes.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.promo-codes.update', $promoCode->id) }}">
                @csrf @method('PUT')
                @include('admin::promo-codes._form', ['promo' => $promoCode])
                <div class="mt-4">
                    <button type="submit" class="btn text-white" style="background:var(--clr-primary);">Update Promo Code</button>
                </div>
            </form>
        </div>
    </div>

</x-admin::layouts.master>
