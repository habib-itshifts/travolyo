@php
    $deal = $deal ?? null;
    $isVendor = $isVendor ?? false;
    $existingRates = old('rates', $deal?->rates?->map(fn($r) => [
        'travel_date_start' => $r->travel_date_start->format('Y-m-d'),
        'travel_date_end'   => $r->travel_date_end->format('Y-m-d'),
        'price_sgl_bb'      => $r->price_sgl_bb,
        'price_dbl_bb'      => $r->price_dbl_bb,
        'extra_bed_price'   => $r->extra_bed_price,
        'child_price'       => $r->child_price,
        'child_breakfast'   => $r->child_breakfast,
    ])?->toArray() ?? []);
    $selectedPromos = old('promo_codes', $deal?->promoCodes?->pluck('id')->toArray() ?? []);
@endphp

<div class="row g-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Room Type <span class="text-danger">*</span></label>
                        <select name="room_type_id" class="form-select @error('room_type_id') is-invalid @enderror" required>
                            <option value="">Select Room Type</option>
                            @foreach ($roomTypes as $rt)
                                <option value="{{ $rt->id }}" {{ old('room_type_id', $deal?->room_type_id) == $rt->id ? 'selected' : '' }}>{{ $rt->name }}</option>
                            @endforeach
                        </select>
                        @error('room_type_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Allocation</label>
                        <input type="text" name="allocation" value="{{ old('allocation', $deal?->allocation) }}" class="form-control" placeholder="e.g. 09 Rooms">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Release Period</label>
                        <input type="text" name="release_period" value="{{ old('release_period', $deal?->release_period) }}" class="form-control" placeholder="e.g. 04 Days Prior">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Booking Window</label>
                        <input type="text" name="booking_window" value="{{ old('booking_window', $deal?->booking_window) }}" class="form-control" placeholder="e.g. Open Window">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Cancellation Policy</label>
                        <input type="text" name="cancellation_policy" value="{{ old('cancellation_policy', $deal?->cancellation_policy) }}" class="form-control" placeholder="e.g. NRF or 48 Hours Prior">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Max Occupancy Label</label>
                        <input type="text" name="max_occupancy_label" value="{{ old('max_occupancy_label', $deal?->max_occupancy_label) }}" class="form-control" placeholder="e.g. 02 Adults +01 Child">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Blackout Dates</label>
                        <input type="text" name="blackout_dates" value="{{ old('blackout_dates', $deal ? implode(', ', $deal->blackout_dates ?? []) : '') }}" class="form-control" placeholder="e.g. 2026-12-24, 2026-12-25">
                        <div class="form-text">Comma-separated dates (YYYY-MM-DD)</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Special Remarks</label>
                        <textarea name="special_remarks" rows="2" class="form-control">{{ old('special_remarks', $deal?->special_remarks) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Rates --}}
        <div class="card border-0 shadow-sm rounded-3 mt-4">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center border-bottom">
                <span class="fw-semibold">Rates</span>
                <button type="button" class="btn btn-sm btn-outline-dark" id="add-rate-row">+ Add Rate</button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0 align-middle" style="font-size:12px;" id="rates-table">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" style="min-width:130px;">Date Start</th>
                                <th style="min-width:130px;">Date End</th>
                                <th style="min-width:100px;">SGL BB</th>
                                <th style="min-width:100px;">DBL BB</th>
                                <th style="min-width:100px;">Extra Bed</th>
                                <th style="min-width:100px;">Child</th>
                                <th style="min-width:100px;">Child BF</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="rates-body">
                            @foreach ($existingRates as $i => $rate)
                            <tr>
                                <td class="ps-3"><input type="date" name="rates[{{ $i }}][travel_date_start]" value="{{ $rate['travel_date_start'] ?? '' }}" class="form-control form-control-sm"></td>
                                <td><input type="date" name="rates[{{ $i }}][travel_date_end]" value="{{ $rate['travel_date_end'] ?? '' }}" class="form-control form-control-sm"></td>
                                <td><input type="number" step="0.01" name="rates[{{ $i }}][price_sgl_bb]" value="{{ $rate['price_sgl_bb'] ?? '' }}" class="form-control form-control-sm" placeholder="0.00"></td>
                                <td><input type="number" step="0.01" name="rates[{{ $i }}][price_dbl_bb]" value="{{ $rate['price_dbl_bb'] ?? '' }}" class="form-control form-control-sm" placeholder="0.00"></td>
                                <td><input type="number" step="0.01" name="rates[{{ $i }}][extra_bed_price]" value="{{ $rate['extra_bed_price'] ?? '' }}" class="form-control form-control-sm" placeholder="0.00"></td>
                                <td><input type="number" step="0.01" name="rates[{{ $i }}][child_price]" value="{{ $rate['child_price'] ?? '' }}" class="form-control form-control-sm" placeholder="0.00"></td>
                                <td><input type="number" step="0.01" name="rates[{{ $i }}][child_breakfast]" value="{{ $rate['child_breakfast'] ?? '' }}" class="form-control form-control-sm" placeholder="0.00"></td>
                                <td><button type="button" class="btn btn-sm btn-outline-danger remove-rate-row">&times;</button></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if (empty($existingRates))
                    <p class="text-muted text-center py-3 mb-0" id="no-rates-msg" style="font-size:13px;">No rates yet. Click "+ Add Rate".</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        @if (!$isVendor)
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-transparent fw-semibold border-bottom">Status</div>
            <div class="card-body">
                <select name="status" class="form-select">
                    <option value="draft" {{ old('status', $deal?->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ old('status', $deal?->status) === 'published' ? 'selected' : '' }}>Published</option>
                </select>
            </div>
        </div>
        @endif

        <div class="card border-0 shadow-sm rounded-3 {{ $isVendor ? '' : 'mt-4' }}">
            <div class="card-header bg-transparent fw-semibold border-bottom">Promo Codes</div>
            <div class="card-body">
                @if ($promoCodes->isEmpty())
                    <p class="text-muted mb-0" style="font-size:13px;">No promo codes available.</p>
                @else
                    <select name="promo_codes[]" class="form-select" multiple size="5">
                        @foreach ($promoCodes as $pc)
                            <option value="{{ $pc->id }}" {{ in_array($pc->id, $selectedPromos) ? 'selected' : '' }}>{{ $pc->code }} - {{ $pc->label ?? $pc->type }}</option>
                        @endforeach
                    </select>
                    <div class="form-text">Hold Ctrl/Cmd to select multiple</div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let idx = {{ count($existingRates) }};
    const tbody = document.getElementById('rates-body');
    const noMsg = document.getElementById('no-rates-msg');

    document.getElementById('add-rate-row').addEventListener('click', function () {
        if (noMsg) noMsg.style.display = 'none';
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="ps-3"><input type="date" name="rates[${idx}][travel_date_start]" class="form-control form-control-sm"></td>
            <td><input type="date" name="rates[${idx}][travel_date_end]" class="form-control form-control-sm"></td>
            <td><input type="number" step="0.01" name="rates[${idx}][price_sgl_bb]" class="form-control form-control-sm" placeholder="0.00"></td>
            <td><input type="number" step="0.01" name="rates[${idx}][price_dbl_bb]" class="form-control form-control-sm" placeholder="0.00"></td>
            <td><input type="number" step="0.01" name="rates[${idx}][extra_bed_price]" class="form-control form-control-sm" placeholder="0.00"></td>
            <td><input type="number" step="0.01" name="rates[${idx}][child_price]" class="form-control form-control-sm" placeholder="0.00"></td>
            <td><input type="number" step="0.01" name="rates[${idx}][child_breakfast]" class="form-control form-control-sm" placeholder="0.00"></td>
            <td><button type="button" class="btn btn-sm btn-outline-danger remove-rate-row">&times;</button></td>`;
        tbody.appendChild(tr);
        idx++;
    });

    tbody.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-rate-row')) e.target.closest('tr').remove();
    });
});
</script>
@endpush
