@php $sup = $hotelDealSupplement ?? null; @endphp

<div class="row g-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Deal <span class="text-danger">*</span></label>
                        <select name="hotel_deal_id" class="form-select @error('hotel_deal_id') is-invalid @enderror" required>
                            <option value="">Select Deal</option>
                            @foreach ($deals as $d)
                                <option value="{{ $d->id }}" {{ old('hotel_deal_id', $sup?->hotel_deal_id) == $d->id ? 'selected' : '' }}>
                                    #{{ $d->id }} - {{ $d->hotel?->name }} / {{ $d->roomType?->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('hotel_deal_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Event Name <span class="text-danger">*</span></label>
                        <input type="text" name="event_name" value="{{ old('event_name', $sup?->event_name) }}" class="form-control @error('event_name') is-invalid @enderror" required placeholder="e.g. Eid Al Fitr, Arab Health">
                        @error('event_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Date Start <span class="text-danger">*</span></label>
                        <input type="date" name="date_start" value="{{ old('date_start', $sup?->date_start?->format('Y-m-d')) }}" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Date End <span class="text-danger">*</span></label>
                        <input type="date" name="date_end" value="{{ old('date_end', $sup?->date_end?->format('Y-m-d')) }}" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Amount (PRPN) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" value="{{ old('amount', $sup?->amount) }}" class="form-control" required min="0">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
