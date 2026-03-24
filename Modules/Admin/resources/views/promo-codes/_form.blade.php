@php $promo = $promo ?? null; @endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Code <span class="text-danger">*</span></label>
        <input type="text" name="code" value="{{ old('code', $promo?->code) }}"
               class="form-control @error('code') is-invalid @enderror" required>
        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Label</label>
        <input type="text" name="label" value="{{ old('label', $promo?->label) }}"
               class="form-control @error('label') is-invalid @enderror">
        @error('label') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Type <span class="text-danger">*</span></label>
        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
            @foreach (['online','offline','contracted','flash_sale'] as $t)
                <option value="{{ $t }}" {{ old('type', $promo?->type) === $t ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$t)) }}</option>
            @endforeach
        </select>
        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Discount Type</label>
        <select name="discount_type" class="form-select @error('discount_type') is-invalid @enderror">
            <option value="">None</option>
            <option value="percent" {{ old('discount_type', $promo?->discount_type) === 'percent' ? 'selected' : '' }}>Percent (%)</option>
            <option value="fixed" {{ old('discount_type', $promo?->discount_type) === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
        </select>
        @error('discount_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Discount Value</label>
        <input type="number" name="discount_value" step="0.01" value="{{ old('discount_value', $promo?->discount_value) }}"
               class="form-control @error('discount_value') is-invalid @enderror">
        @error('discount_value') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-12">
        <label class="form-label">Description</label>
        <input type="text" name="description" value="{{ old('description', $promo?->description) }}"
               class="form-control @error('description') is-invalid @enderror">
        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Valid From</label>
        <input type="date" name="valid_from" value="{{ old('valid_from', $promo?->valid_from?->format('Y-m-d')) }}"
               class="form-control @error('valid_from') is-invalid @enderror">
        @error('valid_from') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Valid Until</label>
        <input type="date" name="valid_until" value="{{ old('valid_until', $promo?->valid_until?->format('Y-m-d')) }}"
               class="form-control @error('valid_until') is-invalid @enderror">
        @error('valid_until') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" id="is_active"
                   class="form-check-input" {{ old('is_active', $promo?->is_active ?? true) ? 'checked' : '' }}>
            <label for="is_active" class="form-check-label">Active</label>
        </div>
    </div>
</div>
