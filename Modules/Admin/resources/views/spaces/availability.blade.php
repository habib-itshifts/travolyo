<x-admin::layouts.master title="Manage Availability">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" style="font-size:12px;">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.spaces.index') }}" class="text-decoration-none">Spaces</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.spaces.show', $space->id) }}" class="text-decoration-none">{{ $space->name }}</a></li>
                    <li class="breadcrumb-item active">Availability</li>
                </ol>
            </nav>
            <h5 class="fw-bold mb-0 text-dark">Manage Availability: {{ $space->name }}</h5>
        </div>
    </div>

    {{-- Update Form --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white border-bottom">
            <h6 class="fw-bold mb-0">Update Availability</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ request()->routeIs('admin.*') ? route('admin.spaces.availability.update', $space->id) : route('vendor.spaces.availability.update', $space->id) }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" style="font-size:13px;">Select Dates</label>
                        <input type="text" id="date-range-picker" name="date_range" class="form-control form-control-sm" placeholder="Select date range" readonly>
                        <div id="selected-dates-container" class="mt-2 d-flex flex-wrap gap-1"></div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold" style="font-size:13px;">Status</label>
                        <select name="is_available" class="form-select form-select-sm">
                            <option value="1">Available</option>
                            <option value="0">Blocked</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Price Override (optional)</label>
                        <input type="number" name="price_override" step="0.01" min="0" class="form-control form-control-sm" placeholder="Custom price...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Notes</label>
                        <input type="text" name="notes" class="form-control form-control-sm" placeholder="e.g. Holiday pricing">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-sm text-white" style="background:var(--clr-primary);">Update Availability</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Current Availability --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-bottom">
            <h6 class="fw-bold mb-0">Current Availability Calendar</h6>
        </div>
        <div class="card-body p-0">
            @if ($availabilities->isEmpty())
                <div class="text-center text-muted py-5">
                    <p class="mb-0">No availability records yet. All dates default to available at the base price.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle" style="font-size:13px;">
                        <thead class="table-light border-bottom">
                            <tr>
                                <th class="ps-4 py-3">Date</th>
                                <th class="py-3">Status</th>
                                <th class="py-3">Price Override</th>
                                <th class="py-3">Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($availabilities as $avail)
                                <tr>
                                    <td class="ps-4">{{ $avail->date->format('D, M d, Y') }}</td>
                                    <td>
                                        @if ($avail->is_available)
                                            <span class="badge bg-success-subtle text-success rounded-pill">Available</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger rounded-pill">Blocked</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($avail->price_override)
                                            {{ $space->currency }} {{ number_format($avail->price_override, 2) }}
                                        @else
                                            <span class="text-muted">&mdash;</span>
                                        @endif
                                    </td>
                                    <td>{{ $avail->notes ?: '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('selected-dates-container');
            const picker = document.getElementById('date-range-picker');

            function toDateString(date) {
                const year = date.getUTCFullYear();
                const month = String(date.getUTCMonth() + 1).padStart(2, '0');
                const day = String(date.getUTCDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            function buildRange(startDate, endDate) {
                const dates = [];
                const current = new Date(startDate);
                const end = new Date(endDate);
                while (current <= end) {
                    dates.push(toDateString(current));
                    current.setUTCDate(current.getUTCDate() + 1);
                }
                return dates;
            }

            function renderDates(dates) {
                container.innerHTML = '';
                dates.forEach((date) => {
                    const badge = document.createElement('span');
                    badge.className = 'badge bg-light text-dark border d-inline-flex align-items-center gap-1';
                    badge.innerHTML = `
                        <input type="hidden" name="dates[]" value="${date}">
                        ${date}
                        <button type="button" class="btn-close" style="font-size:8px; line-height:1;" aria-label="Remove"></button>
                    `;
                    badge.querySelector('.btn-close').addEventListener('click', () => {
                        badge.remove();
                        const remaining = Array.from(container.querySelectorAll('input[name="dates[]"]')).map((input) => input.value);
                        if (remaining.length === 0) {
                            picker.value = '';
                        }
                    });
                    container.appendChild(badge);
                });
            }

            flatpickr(picker, {
                mode: 'range',
                dateFormat: 'Y-m-d',
                allowInput: false,
                minDate: 'today',
                onChange: function(selectedDates, dateStr) {
                    container.innerHTML = '';
                    if (selectedDates.length === 2) {
                        const rangeDates = buildRange(selectedDates[0], selectedDates[1]);
                        picker.value = `${dateStr}`;
                        renderDates(rangeDates);
                    } else if (selectedDates.length === 1) {
                        const singleDate = toDateString(selectedDates[0]);
                        picker.value = singleDate;
                        renderDates([singleDate]);
                    } else {
                        picker.value = '';
                    }
                },
            });
        });
    </script>
    @endpush
</x-admin::layouts.master>
