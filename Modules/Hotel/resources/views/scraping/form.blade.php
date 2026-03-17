@php
    $showStatus = $showStatus ?? false;
    $statusOptions = $statusOptions ?? [];
    $submitLabel = $submitLabel ?? 'Import Hotel';
@endphp

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="font-size:13px;">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger mb-4" role="alert" style="font-size:13px;">
        <strong class="d-block mb-1">Please fix the highlighted fields.</strong>
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-4">
        <form method="POST" action="{{ $action }}">
            @csrf

            <div class="mb-3">
                <label for="url" class="form-label fw-semibold">Hotel URL</label>
                <input
                    id="url"
                    type="url"
                    name="url"
                    value="{{ old('url') }}"
                    class="form-control @error('url') is-invalid @enderror"
                    placeholder="https://www.booking.com/hotel/..."
                    required
                >
                <div class="form-text">
                    Paste the public hotel page URL you want to import.
                </div>
            </div>

            @if ($showStatus)
                <div class="mb-3">
                    <label for="status" class="form-label fw-semibold">Imported Hotel Status</label>
                    <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                        @foreach ($statusOptions as $statusOption)
                            <option value="{{ $statusOption->value }}" @selected(old('status', 'draft') === $statusOption->value)>
                                {{ ucfirst($statusOption->value) }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">
                        Vendors always import as draft. Admin can choose the starting status here.
                    </div>
                </div>
            @endif

            <div class="rounded-3 p-3 mb-4" style="background:#f8fafc;border:1px solid rgba(15,23,42,.06);">
                <p class="mb-1 fw-semibold text-dark">What gets imported</p>
                <p class="mb-0 text-muted" style="font-size:13px;">
                    Hotel details, hotel amenities, services when available, and room data will be mapped into the existing hotel module.
                </p>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn text-white" style="background:var(--clr-primary);">
                    {{ $submitLabel }}
                </button>
                <a href="{{ $cancelUrl }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
