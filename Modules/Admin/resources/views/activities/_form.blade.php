@php
    $isEdit   = isset($activity);
    $action   = $isEdit ? route('admin.activities.update', $activity) : route('admin.activities.store');
    $isVendor = false;
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

        {{-- ── Admin sidebar (col-lg-4) ──────────────────────────────────── --}}
        <div class="col-lg-4">

            {{-- Publish --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-3">Publish</h6>
                    <div class="d-grid gap-2 mb-4">
                        @foreach (['publish' => 'Publish', 'draft' => 'Draft'] as $value => $label)
                            <label class="status-radio">
                                <input type="radio" name="status" value="{{ $value }}"
                                       {{ old('status', $activity->status ?? 'publish') === $value ? 'checked' : '' }}>
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold" style="font-size:13px;">Author</label>
                        <select name="author_id" class="form-select @error('author_id') is-invalid @enderror">
                            <option value="">Select user</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ (string) old('author_id', $activity->author_id ?? auth()->id()) === (string) $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('author_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="card-footer bg-white border-top d-flex gap-2">
                    <button type="submit" class="btn btn-sm flex-fill text-white fw-semibold"
                            style="background:var(--clr-primary);border-radius:var(--radius-btn);">
                        {{ $isEdit ? 'Save Activity' : 'Create Activity' }}
                    </button>
                    <a href="{{ route('admin.activities.index') }}" class="btn btn-sm btn-outline-secondary">Cancel</a>
                </div>
            </div>

            {{-- Feature Image --}}
            <div class="card border-0 shadow-sm rounded-4">
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

@include('admin::media.partials.browser-modal')
