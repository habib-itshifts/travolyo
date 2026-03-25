@php
    $rt = $roomType ?? null;
    $selectedAmenities = $rt ? $rt->amenities->pluck('id')->all() : old('amenity_ids', []);
@endphp

<div class="row g-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $rt?->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">View Type</label>
                        <select name="view_type" class="form-select">
                            <option value="">None</option>
                            @foreach (['sea', 'garden', 'pool', 'city', 'mountain'] as $vt)
                                <option value="{{ $vt }}" {{ old('view_type', $rt?->view_type) === $vt ? 'selected' : '' }}>{{ ucfirst($vt) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" rows="3" class="form-control">{{ old('description', $rt?->description) }}</textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Size (sqm)</label>
                        <input type="number" step="0.01" name="size_sqm" value="{{ old('size_sqm', $rt?->size_sqm) }}" class="form-control">
                    </div>
                </div>
            </div>
        </div>

        @if (isset($amenities) && $amenities->count())
        <div class="card border-0 shadow-sm rounded-3 mt-4">
            <div class="card-header bg-transparent fw-semibold border-bottom">Room Amenities</div>
            <div class="card-body">
                <div class="row g-2">
                    @foreach ($amenities->groupBy('category') as $category => $items)
                        <div class="col-12">
                            <p class="text-uppercase fw-bold mb-2 mt-2" style="font-size:11px;color:var(--clr-primary);letter-spacing:.05em;">{{ ucwords(str_replace('_', ' ', $category)) }}</p>
                        </div>
                        @foreach ($items as $amenity)
                            <div class="col-md-4 col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="amenity_ids[]" value="{{ $amenity->id }}" id="ra_{{ $amenity->id }}" {{ in_array($amenity->id, $selectedAmenities) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="ra_{{ $amenity->id }}">{{ $amenity->name }}</label>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <div class="card border-0 shadow-sm rounded-3 mt-4">
            <div class="card-header bg-transparent fw-semibold border-bottom">Bed Configuration</div>
            <div class="card-body">
                <div class="row g-3">
                    @php
                        $bedTypes = ['king', 'queen', 'twin', 'single', 'sofa_bed', 'bunk'];
                        $bedConfig = old('bed_configuration', $rt?->bed_configuration ?? []);
                    @endphp
                    @foreach ($bedTypes as $bed)
                        <div class="col-md-4">
                            <label class="form-label" style="font-size:13px;">{{ ucfirst(str_replace('_', ' ', $bed)) }}</label>
                            <input type="number" name="bed_configuration[{{ $bed }}]" value="{{ $bedConfig[$bed] ?? 0 }}" class="form-control form-control-sm" min="0">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-transparent fw-semibold border-bottom">Capacity</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Max Adults <span class="text-danger">*</span></label>
                    <input type="number" name="max_adults" value="{{ old('max_adults', $rt?->max_adults ?? 2) }}" class="form-control" required min="1">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Max Children <span class="text-danger">*</span></label>
                    <input type="number" name="max_children" value="{{ old('max_children', $rt?->max_children ?? 0) }}" class="form-control" required min="0">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Max Occupancy <span class="text-danger">*</span></label>
                    <input type="number" name="max_occupancy" value="{{ old('max_occupancy', $rt?->max_occupancy ?? 2) }}" class="form-control" required min="1">
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-3 mt-4">
            <div class="card-header bg-transparent fw-semibold border-bottom">Pricing</div>
            <div class="card-body">
                <p class="text-muted mb-3" style="font-size:11px;">Fallback pricing when no deal applies.</p>
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold">SGL BB</label>
                        <input type="number" step="0.01" min="0" name="price_sgl_bb" value="{{ old('price_sgl_bb', $rt?->price_sgl_bb) }}" class="form-control" placeholder="0.00">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">DBL BB</label>
                        <input type="number" step="0.01" min="0" name="price_dbl_bb" value="{{ old('price_dbl_bb', $rt?->price_dbl_bb) }}" class="form-control" placeholder="0.00">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Extra Bed</label>
                        <input type="number" step="0.01" min="0" name="extra_bed_price" value="{{ old('extra_bed_price', $rt?->extra_bed_price) }}" class="form-control" placeholder="0.00">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Child</label>
                        <input type="number" step="0.01" min="0" name="child_price" value="{{ old('child_price', $rt?->child_price) }}" class="form-control" placeholder="0.00">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Child BF</label>
                        <input type="number" step="0.01" min="0" name="child_breakfast" value="{{ old('child_breakfast', $rt?->child_breakfast) }}" class="form-control" placeholder="0.00">
                    </div>
                </div>
                <hr class="my-3">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Extra Adult Price <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="extra_adult_price" value="{{ old('extra_adult_price', $rt?->extra_adult_price ?? 0) }}" class="form-control" required min="0">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Extra Child Price <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="extra_child_price" value="{{ old('extra_child_price', $rt?->extra_child_price ?? 0) }}" class="form-control" required min="0">
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-3 mt-4">
            <div class="card-body">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                           {{ old('is_active', $rt?->is_active ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="is_active">Active</label>
                </div>
            </div>
        </div>
    </div>
</div>
