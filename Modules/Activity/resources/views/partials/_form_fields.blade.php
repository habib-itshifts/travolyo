{{--
    Shared activity form fields — col-lg-8 content.
    Included by both admin and vendor _form.blade.php.

    Expects from including template:
      $activity       (Activity|null)  existing record or null on create
      $categoryOptions (array)         from ActivityService / CATEGORY_OPTIONS
      $isVendor        (bool)          hides admin-only fields when true
--}}
@php
    $isVendor         = $isVendor ?? false;
    $galleryValue     = (string) old('gallery', $activity->gallery ?? '');
    $galleryIds       = collect(explode(',', $galleryValue))
                            ->map(fn ($id) => (int) trim($id))
                            ->filter()
                            ->values();
    $galleryMedia     = $galleryIds->isNotEmpty()
                            ? \Modules\Admin\Models\MediaFile::whereIn('id', $galleryIds->all())->get()->keyBy('id')
                            : collect();
    $galleryImages    = $galleryIds->map(fn ($id) => optional($galleryMedia->get($id))->url)
                            ->filter()->values()->all();
    $extraInformation = old('extra_information', $activity->extra_information ?? []);
    if (empty($extraInformation)) {
        $extraInformation = [''];
    }
@endphp

<div class="col-lg-8">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">

            {{-- Basics --}}
            <div class="border-bottom pb-4 mb-4">
                <h6 class="fw-bold text-dark mb-3">Basics</h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" style="font-size:13px;">Activity Name <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="activityTitle" value="{{ old('title', $activity->title ?? '') }}"
                               class="form-control @error('title') is-invalid @enderror" placeholder="e.g. Desert Safari Experience">
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" style="font-size:13px;">Category</label>
                        <select name="category" class="form-select @error('category') is-invalid @enderror">
                            <option value="">Select Category</option>
                            @foreach ($categoryOptions as $option)
                                <option value="{{ $option }}" {{ old('category', $activity->category ?? '') === $option ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                        @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" style="font-size:13px;">City</label>
                        <input type="text" name="city" value="{{ old('city', $activity->city ?? '') }}"
                               class="form-control @error('city') is-invalid @enderror" placeholder="e.g. Dubai">
                        @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" style="font-size:13px;">Slug</label>
                        <input type="text" name="slug" id="activitySlug" value="{{ old('slug', $activity->slug ?? '') }}"
                               class="form-control @error('slug') is-invalid @enderror" placeholder="auto-generated from title">
                        <div class="form-text">Leave empty to auto-generate.</div>
                        @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" style="font-size:13px;">Country</label>
                        <input type="text" name="country" value="{{ old('country', $activity->country ?? 'UAE') }}"
                               class="form-control @error('country') is-invalid @enderror" placeholder="UAE">
                        @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" style="font-size:13px;">Address</label>
                        <input type="text" name="address" value="{{ old('address', $activity->address ?? '') }}"
                               class="form-control @error('address') is-invalid @enderror" placeholder="Pickup point or full address">
                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            {{-- Pricing & Capacity --}}
            <div class="border-bottom pb-4 mb-4">
                <h6 class="fw-bold text-dark mb-3">Pricing & Capacity</h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" style="font-size:13px;">Price Per Person</label>
                        <input type="number" step="0.01" min="0" name="price_per_person"
                               value="{{ old('price_per_person', $activity->price_per_person ?? '') }}"
                               class="form-control @error('price_per_person') is-invalid @enderror" placeholder="e.g. 150.00">
                        @error('price_per_person') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" style="font-size:13px;">Currency</label>
                        <input type="text" name="currency" value="{{ old('currency', $activity->currency ?? 'AED') }}"
                               class="form-control @error('currency') is-invalid @enderror" placeholder="AED">
                        @error('currency') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" style="font-size:13px;">Max Participants</label>
                        <input type="number" min="1" name="max_participants"
                               value="{{ old('max_participants', $activity->max_participants ?? '') }}"
                               class="form-control @error('max_participants') is-invalid @enderror" placeholder="50">
                        @error('max_participants') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" style="font-size:13px;">Duration</label>
                        <input type="text" name="duration" value="{{ old('duration', $activity->duration ?? '') }}"
                               class="form-control @error('duration') is-invalid @enderror" placeholder="e.g. 3 Hours, Full Day">
                        @error('duration') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            {{-- Booking Options --}}
            <div class="border-bottom pb-4 mb-4">
                <h6 class="fw-bold text-dark mb-3">Booking Options</h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="checkbox-chip">
                            <input type="checkbox" name="instant_confirmation" value="1"
                                   {{ old('instant_confirmation', $activity->instant_confirmation ?? true) ? 'checked' : '' }}>
                            <span>Instant Confirmation</span>
                        </label>
                    </div>
                    @if (! $isVendor)
                        <div class="col-md-4">
                            <label class="checkbox-chip">
                                <input type="checkbox" name="is_active" value="1"
                                       {{ old('is_active', $activity->is_active ?? true) ? 'checked' : '' }}>
                                <span>Active</span>
                            </label>
                        </div>
                    @endif
                    <div class="col-md-4">
                        <label class="checkbox-chip">
                            <input type="checkbox" name="email_flyer_enabled" value="1"
                                   {{ old('email_flyer_enabled', $activity->email_flyer_enabled ?? true) ? 'checked' : '' }}>
                            <span>Email Flyer Enabled</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Content --}}
            <div class="border-bottom pb-4 mb-4">
                <h6 class="fw-bold text-dark mb-3">Content</h6>
                <div class="col-12">
                    <label class="form-label fw-semibold" style="font-size:13px;">Description</label>
                    <textarea name="description" rows="8" class="form-control @error('description') is-invalid @enderror"
                              placeholder="Write activity details, highlights, and inclusions...">{{ old('description', $activity->description ?? '') }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- Extra Information --}}
            <div class="border-bottom pb-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="fw-bold text-dark mb-0">Extra Information</h6>
                        <p class="text-muted mb-0" style="font-size:12px;">Highlights, inclusions, exclusions, or helpful notes for this activity.</p>
                    </div>
                    <button type="button" class="btn btn-sm btn-info text-white" id="addExtraInfoItem">Add item</button>
                </div>
                <div id="extra-information-list" class="d-grid gap-3">
                    @foreach ($extraInformation as $index => $item)
                        <div class="border rounded-3 p-3 extra-info-item" data-extra-info-item>
                            <div class="row g-3 align-items-end">
                                <div class="col-md-11">
                                    <label class="form-label fw-semibold" style="font-size:13px;">Item</label>
                                    <input type="text" name="extra_information[{{ $index }}]" value="{{ $item }}"
                                           class="form-control" placeholder="e.g. Private transport, entry tickets, professional guide">
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-sm btn-outline-danger w-100" data-remove-extra-info>x</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Gallery --}}
            <div>
                <h6 class="fw-bold text-dark mb-3">Media</h6>
                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:13px;">Activity Gallery</label>
                    <div class="d-flex gap-2 mb-2">
                        <button type="button" class="btn btn-info btn-sm text-white"
                                data-open-media-browser data-media-target="gallery" data-media-multiple="true">
                            Select images
                        </button>
                    </div>
                    <input type="hidden" name="gallery" id="gallery-image-ids" value="{{ $galleryValue }}">
                    <div class="d-flex flex-wrap gap-2 mt-2" id="gallery-images-preview">
                        @foreach ($galleryImages as $image)
                            <img src="{{ $image }}" alt="Gallery Image" class="rounded border"
                                 style="width:110px;height:90px;object-fit:cover;">
                        @endforeach
                    </div>
                    @error('gallery') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Extra information JS template --}}
<template id="extra-information-template">
    <div class="border rounded-3 p-3 extra-info-item" data-extra-info-item>
        <div class="row g-3 align-items-end">
            <div class="col-md-11">
                <label class="form-label fw-semibold" style="font-size:13px;">Item</label>
                <input type="text" name="extra_information[__INDEX__]" class="form-control"
                       placeholder="e.g. Private transport, entry tickets, professional guide">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-outline-danger w-100" data-remove-extra-info>x</button>
            </div>
        </div>
    </div>
</template>

@push('styles')
<style>
    .media-picker-card {
        border: 1px solid #dbe3ef;
        border-radius: 12px;
        background: #f8fafc;
        min-height: 210px;
        padding: 18px;
        cursor: pointer;
        transition: .2s ease;
    }
    .media-picker-card:hover {
        border-color: var(--clr-primary);
        box-shadow: 0 0 0 3px rgba(58, 181, 212, 0.12);
    }
    .media-picker-preview {
        min-height: 170px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .media-picker-preview img {
        max-width: 100%;
        max-height: 170px;
        object-fit: cover;
    }
    .media-picker-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 16px;
        width: 100%;
        color: #94a3b8;
    }
    .media-picker-icon { font-size: 54px; line-height: 1; }
    .checkbox-chip,
    .status-radio {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 14px;
        border: 1px solid #dbe3ef;
        border-radius: 14px;
        background: #fff;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        color: #334155;
    }
    .checkbox-chip input,
    .status-radio input { margin: 0; accent-color: var(--clr-primary); }
</style>
@endpush

@push('scripts')
<script>
    (() => {
        const titleInput = document.getElementById('activityTitle');
        const slugInput  = document.getElementById('activitySlug');
        const list       = document.getElementById('extra-information-list');
        const template   = document.getElementById('extra-information-template');
        const addButton  = document.getElementById('addExtraInfoItem');

        const slugify = (value) =>
            String(value || '').toLowerCase().trim()
                .replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');

        titleInput?.addEventListener('input', () => {
            if (!slugInput || slugInput.dataset.touched === '1') return;
            slugInput.value = slugify(titleInput.value);
        });

        slugInput?.addEventListener('input', () => {
            slugInput.dataset.touched = slugInput.value ? '1' : '0';
        });

        addButton?.addEventListener('click', () => {
            if (!list || !template) return;
            const index = list.querySelectorAll('[data-extra-info-item]').length;
            list.insertAdjacentHTML('beforeend', template.innerHTML.replaceAll('__INDEX__', index));
        });

        document.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-remove-extra-info]');
            if (!btn) return;
            const item  = btn.closest('[data-extra-info-item]');
            const items = list?.querySelectorAll('[data-extra-info-item]') || [];
            if (!item) return;
            if (items.length === 1) {
                const input = item.querySelector('input');
                if (input) input.value = '';
                return;
            }
            item.remove();
        });
    })();
</script>
@endpush
