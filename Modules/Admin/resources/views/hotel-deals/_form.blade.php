@php
    $deal       = $deal ?? null;
    $rates      = old('rates', $deal?->rates?->toArray() ?? []);
    $selectedPromos = old('promo_codes', $deal?->promoCodes?->pluck('id')->toArray() ?? []);
    $blackouts  = old('blackout_dates', $deal?->blackout_dates ?? []);
@endphp

{{-- Deal Info --}}
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label class="form-label">Room Type <span class="text-danger">*</span></label>
        <input type="text" name="room_type" value="{{ old('room_type', $deal?->room_type) }}"
               class="form-control @error('room_type') is-invalid @enderror" required
               placeholder="e.g. Superior Deluxe Room">
        @error('room_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Linked Room (optional)</label>
        <select name="hotel_room_id" class="form-select @error('hotel_room_id') is-invalid @enderror">
            <option value="">— None —</option>
            @foreach ($hotel->rooms as $room)
                <option value="{{ $room->id }}" {{ old('hotel_room_id', $deal?->hotel_room_id) == $room->id ? 'selected' : '' }}>
                    {{ $room->name }}
                </option>
            @endforeach
        </select>
        @error('hotel_room_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Release Period</label>
        <input type="text" name="release_period" value="{{ old('release_period', $deal?->release_period) }}"
               class="form-control" placeholder="e.g. 04 Days Prior">
    </div>
    <div class="col-md-4">
        <label class="form-label">Booking Window</label>
        <input type="text" name="booking_window" value="{{ old('booking_window', $deal?->booking_window) }}"
               class="form-control" placeholder="e.g. Open Window">
    </div>
    <div class="col-md-4">
        <label class="form-label">Cancellation Policy</label>
        <input type="text" name="cancellation_policy" value="{{ old('cancellation_policy', $deal?->cancellation_policy) }}"
               class="form-control" placeholder="e.g. NRF or 48 Hours Prior">
    </div>
    <div class="col-md-4">
        <label class="form-label">Max Occupancy</label>
        <input type="text" name="max_occupancy_label" value="{{ old('max_occupancy_label', $deal?->max_occupancy_label) }}"
               class="form-control" placeholder="e.g. 02 Adults + 01 child">
    </div>
    <div class="col-md-4">
        <label class="form-label">Allocation</label>
        <input type="text" name="allocation" value="{{ old('allocation', $deal?->allocation) }}"
               class="form-control" placeholder="e.g. 09 Rooms">
    </div>
    <div class="col-md-4">
        <label class="form-label">Status <span class="text-danger">*</span></label>
        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
            <option value="draft" {{ old('status', $deal?->status) === 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="published" {{ old('status', $deal?->status) === 'published' ? 'selected' : '' }}>Published</option>
        </select>
        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Promo Codes</label>
        <select name="promo_codes[]" class="form-select" multiple style="min-height:80px;">
            @foreach ($promoCodes as $promo)
                <option value="{{ $promo->id }}" {{ in_array($promo->id, $selectedPromos) ? 'selected' : '' }}>
                    {{ $promo->code }} — {{ $promo->label }}
                </option>
            @endforeach
        </select>
        <small class="text-muted">Hold Ctrl/Cmd to select multiple</small>
    </div>
    <div class="col-md-6">
        <label class="form-label">Blackout Dates</label>
        <input type="text" id="blackout-input" class="form-control" placeholder="Type a date (YYYY-MM-DD) and press Enter">
        <div id="blackout-tags" class="d-flex flex-wrap gap-1 mt-2">
            @foreach ($blackouts as $i => $date)
                <span class="badge bg-danger-subtle text-danger border d-inline-flex align-items-center gap-1">
                    {{ $date }}
                    <input type="hidden" name="blackout_dates[]" value="{{ $date }}">
                    <button type="button" class="btn-close btn-close-sm" style="font-size:8px;" onclick="this.parentElement.remove()"></button>
                </span>
            @endforeach
        </div>
    </div>
    <div class="col-12">
        <label class="form-label">Special Remarks</label>
        <textarea name="special_remarks" class="form-control" rows="2">{{ old('special_remarks', $deal?->special_remarks) }}</textarea>
    </div>
</div>

{{-- Rates (inline dynamic rows) --}}
<div class="border rounded-3 p-3 mb-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h6 class="fw-semibold mb-0">Rates</h6>
        <button type="button" class="btn btn-sm btn-outline-dark" id="add-rate-btn">+ Add Rate Row</button>
    </div>
    <div id="rates-container">
        @foreach ($rates as $i => $rate)
            <div class="rate-row border rounded-2 p-3 mb-2">
                <div class="d-flex justify-content-between mb-2">
                    <small class="fw-semibold text-muted">Rate #{{ $i + 1 }}</small>
                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-rate" style="font-size:11px;">Remove</button>
                </div>
                <div class="row g-2">
                    <div class="col-md-3">
                        <label class="form-label" style="font-size:12px;">Travel Start <span class="text-danger">*</span></label>
                        <input type="date" name="rates[{{ $i }}][travel_date_start]"
                               value="{{ is_array($rate) ? ($rate['travel_date_start'] ?? '') : ($rate->travel_date_start?->format('Y-m-d') ?? '') }}"
                               class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-size:12px;">Travel End <span class="text-danger">*</span></label>
                        <input type="date" name="rates[{{ $i }}][travel_date_end]"
                               value="{{ is_array($rate) ? ($rate['travel_date_end'] ?? '') : ($rate->travel_date_end?->format('Y-m-d') ?? '') }}"
                               class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="font-size:12px;">SGL BB</label>
                        <input type="number" step="0.01" name="rates[{{ $i }}][price_sgl_bb]"
                               value="{{ $rate['price_sgl_bb'] ?? '' }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="font-size:12px;">DBL BB</label>
                        <input type="number" step="0.01" name="rates[{{ $i }}][price_dbl_bb]"
                               value="{{ $rate['price_dbl_bb'] ?? '' }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="font-size:12px;">Extra Bed</label>
                        <input type="number" step="0.01" name="rates[{{ $i }}][extra_bed_price]"
                               value="{{ $rate['extra_bed_price'] ?? '' }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="font-size:12px;">Child Price</label>
                        <input type="number" step="0.01" name="rates[{{ $i }}][child_price]"
                               value="{{ $rate['child_price'] ?? '' }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="font-size:12px;">Child Breakfast</label>
                        <input type="number" step="0.01" name="rates[{{ $i }}][child_breakfast]"
                               value="{{ $rate['child_breakfast'] ?? '' }}" class="form-control form-control-sm">
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Add rate row
    let rateIndex = {{ count($rates) }};
    document.getElementById('add-rate-btn').addEventListener('click', function () {
        const html = `
        <div class="rate-row border rounded-2 p-3 mb-2">
            <div class="d-flex justify-content-between mb-2">
                <small class="fw-semibold text-muted">Rate #${rateIndex + 1}</small>
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-rate" style="font-size:11px;">Remove</button>
            </div>
            <div class="row g-2">
                <div class="col-md-3">
                    <label class="form-label" style="font-size:12px;">Travel Start <span class="text-danger">*</span></label>
                    <input type="date" name="rates[${rateIndex}][travel_date_start]" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label" style="font-size:12px;">Travel End <span class="text-danger">*</span></label>
                    <input type="date" name="rates[${rateIndex}][travel_date_end]" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label" style="font-size:12px;">SGL BB</label>
                    <input type="number" step="0.01" name="rates[${rateIndex}][price_sgl_bb]" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label" style="font-size:12px;">DBL BB</label>
                    <input type="number" step="0.01" name="rates[${rateIndex}][price_dbl_bb]" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label" style="font-size:12px;">Extra Bed</label>
                    <input type="number" step="0.01" name="rates[${rateIndex}][extra_bed_price]" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label" style="font-size:12px;">Child Price</label>
                    <input type="number" step="0.01" name="rates[${rateIndex}][child_price]" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label" style="font-size:12px;">Child Breakfast</label>
                    <input type="number" step="0.01" name="rates[${rateIndex}][child_breakfast]" class="form-control form-control-sm">
                </div>
            </div>
        </div>`;
        document.getElementById('rates-container').insertAdjacentHTML('beforeend', html);
        rateIndex++;
    });

    // Remove rate row
    document.getElementById('rates-container').addEventListener('click', function (e) {
        if (e.target.classList.contains('btn-remove-rate')) {
            e.target.closest('.rate-row').remove();
        }
    });

    // Blackout dates tag input
    document.getElementById('blackout-input').addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const val = this.value.trim();
            if (val && /^\d{4}-\d{2}-\d{2}$/.test(val)) {
                const tag = document.createElement('span');
                tag.className = 'badge bg-danger-subtle text-danger border d-inline-flex align-items-center gap-1';
                tag.innerHTML = `${val}<input type="hidden" name="blackout_dates[]" value="${val}"><button type="button" class="btn-close btn-close-sm" style="font-size:8px;" onclick="this.parentElement.remove()"></button>`;
                document.getElementById('blackout-tags').appendChild(tag);
                this.value = '';
            }
        }
    });
});
</script>
@endpush
