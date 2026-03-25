@php
    $deal = $deal ?? null;
    $isVendor = $isVendor ?? false;
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
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Release Period</label>
                        <div class="input-group">
                            <input type="number" min="0" name="release_period" value="{{ old('release_period', $deal?->release_period) }}" class="form-control @error('release_period') is-invalid @enderror" placeholder="e.g. 4">
                            <span class="input-group-text" style="font-size:12px;">days</span>
                        </div>
                        @error('release_period') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Release Type</label>
                        <input type="text" name="release_type" value="{{ old('release_type', $deal?->release_type) }}" class="form-control @error('release_type') is-invalid @enderror" placeholder="e.g. Prior">
                        @error('release_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Booking Window</label>
                        <input type="date" name="booking_window" value="{{ old('booking_window', $deal?->booking_window?->format('Y-m-d')) }}" class="form-control @error('booking_window') is-invalid @enderror">
                        @error('booking_window') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Cancellation Policy</label>
                        <input type="text" name="cancellation_policy" value="{{ old('cancellation_policy', $deal?->cancellation_policy) }}" class="form-control @error('cancellation_policy') is-invalid @enderror" placeholder="e.g. NRF or 48 Hours Prior">
                        @error('cancellation_policy') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Blackout Dates</label>
                        <input type="text" name="blackout_dates" id="blackout-dates-picker" value="{{ old('blackout_dates', $deal ? implode(', ', $deal->blackout_dates ?? []) : '') }}" class="form-control @error('blackout_dates') is-invalid @enderror" placeholder="Click to select dates" readonly>
                        <div class="form-text">Click to pick multiple dates from the calendar</div>
                        @error('blackout_dates') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Special Remarks</label>
                        <textarea name="special_remarks" rows="2" class="form-control @error('special_remarks') is-invalid @enderror">{{ old('special_remarks', $deal?->special_remarks) }}</textarea>
                        @error('special_remarks') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Rates (single row in same table) --}}
        <div class="card border-0 shadow-sm rounded-3 mt-4">
            <div class="card-header bg-transparent fw-semibold border-bottom">Rates</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Date Start</label>
                        <input type="date" name="travel_date_start" value="{{ old('travel_date_start', $deal?->travel_date_start?->format('Y-m-d')) }}" class="form-control @error('travel_date_start') is-invalid @enderror">
                        @error('travel_date_start') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Date End</label>
                        <input type="date" name="travel_date_end" value="{{ old('travel_date_end', $deal?->travel_date_end?->format('Y-m-d')) }}" class="form-control @error('travel_date_end') is-invalid @enderror">
                        @error('travel_date_end') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">SGL BB</label>
                        <input type="number" step="0.01" min="0" name="price_sgl_bb" value="{{ old('price_sgl_bb', $deal?->price_sgl_bb) }}" class="form-control @error('price_sgl_bb') is-invalid @enderror" placeholder="0.00">
                        @error('price_sgl_bb') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">DBL BB</label>
                        <input type="number" step="0.01" min="0" name="price_dbl_bb" value="{{ old('price_dbl_bb', $deal?->price_dbl_bb) }}" class="form-control @error('price_dbl_bb') is-invalid @enderror" placeholder="0.00">
                        @error('price_dbl_bb') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Extra Bed</label>
                        <input type="number" step="0.01" min="0" name="extra_bed_price" value="{{ old('extra_bed_price', $deal?->extra_bed_price) }}" class="form-control @error('extra_bed_price') is-invalid @enderror" placeholder="0.00">
                        @error('extra_bed_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Child</label>
                        <input type="number" step="0.01" min="0" name="child_price" value="{{ old('child_price', $deal?->child_price) }}" class="form-control @error('child_price') is-invalid @enderror" placeholder="0.00">
                        @error('child_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Child BF</label>
                        <input type="number" step="0.01" min="0" name="child_breakfast" value="{{ old('child_breakfast', $deal?->child_breakfast) }}" class="form-control @error('child_breakfast') is-invalid @enderror" placeholder="0.00">
                        @error('child_breakfast') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
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

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    flatpickr('#blackout-dates-picker', {
        mode: 'multiple',
        dateFormat: 'Y-m-d',
        conjunction: ', ',
        allowInput: false,
    });
</script>
@endpush
