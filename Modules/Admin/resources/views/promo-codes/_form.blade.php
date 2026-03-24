@php $pc = $promoCode ?? null; @endphp

<div class="row g-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" value="{{ old('code', $pc?->code) }}" class="form-control @error('code') is-invalid @enderror" required>
                        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Label</label>
                        <input type="text" name="label" value="{{ old('label', $pc?->label) }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required>
                            @foreach (['online', 'offline', 'contracted', 'flash_sale'] as $t)
                                <option value="{{ $t }}" {{ old('type', $pc?->type ?? 'online') === $t ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $t)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Description</label>
                        <input type="text" name="description" value="{{ old('description', $pc?->description) }}" class="form-control" maxlength="255">
                    </div>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm rounded-3 mt-4">
            <div class="card-header bg-transparent fw-semibold border-bottom">Discount</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Discount Type</label>
                        <select name="discount_type" class="form-select">
                            <option value="">None (label only)</option>
                            <option value="percent" {{ old('discount_type', $pc?->discount_type) === 'percent' ? 'selected' : '' }}>Percentage (%)</option>
                            <option value="fixed" {{ old('discount_type', $pc?->discount_type) === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Discount Value</label>
                        <input type="number" step="0.01" name="discount_value" value="{{ old('discount_value', $pc?->discount_value) }}" class="form-control" min="0">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-transparent fw-semibold border-bottom">Validity</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Valid From</label>
                    <input type="date" name="valid_from" value="{{ old('valid_from', $pc?->valid_from?->format('Y-m-d')) }}" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Valid Until</label>
                    <input type="date" name="valid_until" value="{{ old('valid_until', $pc?->valid_until?->format('Y-m-d')) }}" class="form-control">
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm rounded-3 mt-4">
            <div class="card-body">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                           {{ old('is_active', $pc?->is_active ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="is_active">Active</label>
                </div>
            </div>
        </div>
    </div>
</div>
