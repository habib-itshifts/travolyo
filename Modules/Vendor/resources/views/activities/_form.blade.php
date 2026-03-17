@php
    $isEdit   = isset($activity);
    $action   = $isEdit ? route('vendor.activities.update', $activity) : route('vendor.activities.store');
    $isVendor = true;
    $imageId  = old('image_id', $activity->image_id ?? '');
    $imageUrl = null;
    if ($imageId) {
        $media    = \Modules\Admin\Models\MediaFile::find((int) $imageId);
        $imageUrl = $media?->url;
    }
@endphp

<form method="POST" action="{{ $action }}">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="row g-4">

        {{-- ── Shared fields (col-lg-8) ──────────────────────────────────── --}}
        @include('activity::partials._form_fields')

        {{-- ── Vendor sidebar (col-lg-4) ──────────────────────────────────── --}}
        <div class="col-lg-4">

            {{-- Pending notice --}}
            <div class="alert rounded-4 mb-4 d-flex gap-3 align-items-start"
                 style="background:#fffbeb;border:1px solid #fde68a;font-size:13px;">
                <svg width="18" height="18" fill="none" stroke="#d97706" viewBox="0 0 24 24" class="flex-shrink-0 mt-1">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                <div>
                    <p class="fw-semibold mb-1" style="color:#92400e;">Pending Admin Review</p>
                    <p class="mb-0" style="color:#78350f;">Your activity will be set to <strong>Pending</strong> and must be approved by an admin before it appears publicly.</p>
                </div>
            </div>

            {{-- Feature Image --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-3">Feature Image</h6>
                    <input type="hidden" name="image_id" id="featured-image-id" value="{{ $imageId }}">
                    <div class="media-picker-card" data-open-media-browser data-media-target="featured" data-media-multiple="false">
                        <div class="media-picker-preview" id="featured-image-preview">
                            @if ($imageUrl)
                                <img src="{{ $imageUrl }}" alt="Activity Image" class="img-fluid rounded border" style="max-height:180px;">
                            @else
                                <div class="media-picker-empty">
                                    <div class="media-picker-icon">&#128247;</div>
                                    <span class="btn btn-primary btn-sm">Upload image</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    @error('image_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- Submit --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <button type="submit" class="btn btn-sm w-100 text-white fw-semibold"
                            style="background:var(--clr-primary);border-radius:var(--radius-btn);">
                        {{ $isEdit ? 'Save &amp; Submit for Review' : 'Submit Activity for Review' }}
                    </button>
                    <a href="{{ route('vendor.activities.index') }}" class="btn btn-sm btn-outline-secondary w-100 mt-2">Cancel</a>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger rounded-4 mt-4 mb-0" style="font-size:13px;">
                    <p class="fw-semibold mb-1">Please fix the following errors:</p>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

        </div>
    </div>
</form>

@include('vendor::media.partials.browser-modal')
