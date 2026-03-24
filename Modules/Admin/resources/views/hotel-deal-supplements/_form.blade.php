@php $supplement = $supplement ?? null; @endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Deal <span class="text-danger">*</span></label>
        <select name="hotel_deal_id" class="form-select @error('hotel_deal_id') is-invalid @enderror" required>
            <option value="">— Select Deal —</option>
            @foreach ($deals as $d)
                <option value="{{ $d->id }}" {{ old('hotel_deal_id', $supplement?->hotel_deal_id) == $d->id ? 'selected' : '' }}>
                    #{{ $d->id }} — {{ $d->hotel->name ?? 'Unknown' }} ({{ $d->room_type ?? '' }})
                </option>
            @endforeach
        </select>
        @error('hotel_deal_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Event Name <span class="text-danger">*</span></label>
        <input type="text" name="event_name" value="{{ old('event_name', $supplement?->event_name) }}"
               class="form-control @error('event_name') is-invalid @enderror" required
               placeholder="e.g. Eid Al Fitr, Arab Health">
        @error('event_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Date Start <span class="text-danger">*</span></label>
        <input type="date" name="date_start" value="{{ old('date_start', $supplement?->date_start?->format('Y-m-d')) }}"
               class="form-control @error('date_start') is-invalid @enderror" required>
        @error('date_start') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Date End <span class="text-danger">*</span></label>
        <input type="date" name="date_end" value="{{ old('date_end', $supplement?->date_end?->format('Y-m-d')) }}"
               class="form-control @error('date_end') is-invalid @enderror" required>
        @error('date_end') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Amount (PRPN) <span class="text-danger">*</span></label>
        <input type="number" step="0.01" name="amount" value="{{ old('amount', $supplement?->amount) }}"
               class="form-control @error('amount') is-invalid @enderror" required>
        @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>
