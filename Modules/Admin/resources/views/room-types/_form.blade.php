@php $rt = $roomType ?? null; @endphp

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
            <div class="card-header bg-transparent fw-semibold border-bottom">Extra Pricing</div>
            <div class="card-body">
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
