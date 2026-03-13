{{-- Sticky Booking Widget --}}
@php
    $searchContext = $searchContext ?? [];
    $prefillCheckin = request('checkin', request('check_in', data_get($searchContext, 'checkin', '')));
    $prefillCheckout = request('checkout', request('check_out', data_get($searchContext, 'checkout', '')));
    $prefillAdults = max(1, (int) request('adults', data_get($searchContext, 'adults', request('guests', data_get($searchContext, 'guests', 1)))));
    $prefillChildren = max(0, (int) request('children', data_get($searchContext, 'children', 0)));
    $prefillRooms = max(1, (int) request('rooms', request('unit', data_get($searchContext, 'rooms', data_get($searchContext, 'unit', 1)))));
@endphp

<div class="booking-widget" id="bookingWidget">

    <h6 class="booking-widget__heading">Book Your Stay</h6>

    {{-- Placeholder: shown when no room selected --}}
    <div id="widgetNoRoom" class="booking-widget__no-room">
        <i class="bi bi-cursor"></i>
        <p>Select a room below to see pricing</p>
    </div>

    {{-- Price section: hidden until a room is selected --}}
    <div id="widgetPriceSection" style="display:none;" class="booking-widget__price">
        <span class="booking-widget__price-old" id="widgetPriceOld" style="display:none;"></span>
        <span class="booking-widget__price-current" id="widgetPriceAmount">$0</span>
        <span class="booking-widget__price-unit">/ night</span>
        <div class="booking-widget__room-name" id="widgetRoomName"></div>
    </div>

    <form action="{{ route('frontend.hotels.checkout', $hotel->slug) }}" method="GET" id="bookingForm">
        <input type="hidden" name="room" id="selectedRoomId" value="">

        <div class="booking-widget__dates">
            <div class="booking-widget__field">
                <label>Check-in</label>
                <div class="booking-widget__input-wrap">
                    <i class="bi bi-calendar3"></i>
                    <input type="date" name="checkin" id="checkinDate"
                           value="{{ $prefillCheckin }}"
                           min="{{ date('Y-m-d') }}"
                           class="form-control" required>
                </div>
            </div>
            <div class="booking-widget__field">
                <label>Check-out</label>
                <div class="booking-widget__input-wrap">
                    <i class="bi bi-calendar3"></i>
                    <input type="date" name="checkout" id="checkoutDate"
                           value="{{ $prefillCheckout }}"
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                           class="form-control" required>
                </div>
            </div>
        </div>

        <div class="booking-widget__field mt-3">
            <label>Guests</label>
            <div class="booking-guests-picker" id="detailGuestsPicker">
                <div class="booking-widget__input-wrap guests-trigger" role="button" tabindex="0" aria-expanded="false">
                    <i class="bi bi-people"></i>
                    <input id="guestsSummaryDisplay" type="text" class="form-control guests-summary-input" readonly>
                </div>
                <div class="guests-menu" id="detailGuestsMenu">
                    <div class="guest-row">
                        <div>
                            <div class="guest-label">Adults</div>
                            <div class="guest-sub">Above 12 years</div>
                        </div>
                        <div class="counter-wrap">
                            <button type="button" class="counter-btn" data-counter="adults" data-delta="-1">-</button>
                            <span class="counter-val" id="adultsCounterVal">1</span>
                            <button type="button" class="counter-btn" data-counter="adults" data-delta="1">+</button>
                        </div>
                    </div>
                    <div class="guest-row">
                        <div>
                            <div class="guest-label">Children</div>
                            <div class="guest-sub">Below 12 years</div>
                        </div>
                        <div class="counter-wrap">
                            <button type="button" class="counter-btn" data-counter="children" data-delta="-1">-</button>
                            <span class="counter-val" id="childrenCounterVal">0</span>
                            <button type="button" class="counter-btn" data-counter="children" data-delta="1">+</button>
                        </div>
                    </div>
                    <div class="guest-row">
                        <div>
                            <div class="guest-label">Unit</div>
                            <div class="guest-sub">Rooms</div>
                        </div>
                        <div class="counter-wrap">
                            <button type="button" class="counter-btn" data-counter="rooms" data-delta="-1">-</button>
                            <span class="counter-val" id="roomsCounterVal">1</span>
                            <button type="button" class="counter-btn" data-counter="rooms" data-delta="1">+</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="adults" id="adultsInput" value="{{ $prefillAdults }}">
        <input type="hidden" name="children" id="childrenInput" value="{{ $prefillChildren }}">
        <input type="hidden" name="rooms" id="roomsInput" value="{{ $prefillRooms }}">
        <input type="hidden" name="guests" id="guestsTotalInput" value="{{ max(1, $prefillAdults + $prefillChildren) }}">

        {{-- Price Breakdown (shown only when room + dates selected) --}}
        <div class="booking-widget__breakdown" id="priceBreakdown" style="display:none;">
            <div class="booking-widget__breakdown-row">
                <span><span id="nightsPriceLabel">$0</span> × <span id="nightsCount">0</span> nights</span>
                <span id="subtotalAmount">$0</span>
            </div>
            <div class="booking-widget__breakdown-row">
                <span>Taxes &amp; fees</span>
                <span id="taxAmount">$0</span>
            </div>
            <div class="booking-widget__breakdown-row" id="extrasBreakdownRow" style="display:none;">
                <span>Add-ons</span>
                <span id="extrasAmount">$0</span>
            </div>
            <div class="booking-widget__breakdown-total">
                <span>Total</span>
                <span id="totalAmount">$0</span>
            </div>
        </div>

        {{-- Optional Extra Price Add-ons --}}
        @if($hotel->enable_extra_price && !empty($hotel->extra_price))
        <div class="booking-widget__extras" id="extraPriceSection" style="display:none;">
            <div class="booking-widget__extras-title">Optional Add-ons</div>
            @foreach($hotel->extra_price as $index => $extra)
            <label class="booking-widget__extra-item">
                <input type="checkbox"
                       name="extra_price[{{ $index }}]"
                       value="1"
                       class="extra-price-check"
                       data-index="{{ $index }}"
                       data-price="{{ $extra['price'] ?? 0 }}"
                       data-type="{{ $extra['type'] ?? 'one_time' }}"
                       data-per-person="{{ !empty($extra['per_person']) ? 1 : 0 }}">
                <div class="booking-widget__extra-info">
                    <span class="booking-widget__extra-name">{{ $extra['name'] ?? 'Extra' }}</span>
                    <span class="booking-widget__extra-desc">
                        ${{ number_format($extra['price'] ?? 0) }}{{ ($extra['type'] ?? '') === 'per_day' ? ' / night' : ' one-time' }}{{ !empty($extra['per_person']) ? ' / person' : '' }}
                    </span>
                </div>
            </label>
            @endforeach
        </div>
        @endif

        <button type="submit" class="btn btn-reserve w-100 mt-3">
            <i class="bi bi-credit-card me-2"></i>Reserve Now
        </button>
    </form>

    <div class="booking-widget__trust">
        <div class="booking-widget__trust-item">
            <i class="bi bi-shield-check text-warning"></i>
            <span>Secure Booking</span>
        </div>
        <div class="booking-widget__trust-item">
            <i class="bi bi-check-circle text-success"></i>
            <span>Instant Confirmation</span>
        </div>
        <div class="booking-widget__trust-item">
            <i class="bi bi-tags text-primary"></i>
            <span>Best Price Guarantee</span>
        </div>
        <div class="booking-widget__trust-item">
            <i class="bi bi-headset text-info"></i>
            <span>24/7 Support</span>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const TAX_RATE = 0;
    let currentPricePerNight = 0;
    let roomSelected = false;

    const EXTRA_PRICES = @json($hotel->enable_extra_price ? ($hotel->extra_price ?? []) : []);
    const extraPriceSection = document.getElementById('extraPriceSection');
    const extrasBreakdownRow = document.getElementById('extrasBreakdownRow');

    const checkinEl  = document.getElementById('checkinDate');
    const checkoutEl = document.getElementById('checkoutDate');
    const guestsPicker = document.getElementById('detailGuestsPicker');
    const guestsTrigger = guestsPicker ? guestsPicker.querySelector('.guests-trigger') : null;
    const guestsMenu = document.getElementById('detailGuestsMenu');
    const guestsSummaryDisplay = document.getElementById('guestsSummaryDisplay');
    const adultsInput = document.getElementById('adultsInput');
    const childrenInput = document.getElementById('childrenInput');
    const roomsInput = document.getElementById('roomsInput');
    const adultsCounterVal = document.getElementById('adultsCounterVal');
    const childrenCounterVal = document.getElementById('childrenCounterVal');
    const roomsCounterVal = document.getElementById('roomsCounterVal');
    const guestsTotalInput = document.getElementById('guestsTotalInput');
    const breakdown  = document.getElementById('priceBreakdown');

    function asNumber(value, fallback = 0) {
        const n = Number(value);
        return Number.isFinite(n) ? n : fallback;
    }

    function syncGuestTotals() {
        if (!guestsTotalInput) return;
        const adults = Math.max(1, asNumber(adultsInput ? adultsInput.value : 1, 1));
        const children = Math.max(0, asNumber(childrenInput ? childrenInput.value : 0, 0));
        guestsTotalInput.value = String(adults + children);
        if (adultsCounterVal) adultsCounterVal.textContent = String(adults);
        if (childrenCounterVal) childrenCounterVal.textContent = String(children);
        if (roomsCounterVal) roomsCounterVal.textContent = String(Math.max(1, asNumber(roomsInput ? roomsInput.value : 1, 1)));
        if (guestsSummaryDisplay) {
            guestsSummaryDisplay.value = `${Math.max(1, asNumber(roomsInput ? roomsInput.value : 1, 1))} Unit - ${adults} Adult - ${children} Children`;
        }
    }

    function setupGuestsPicker() {
        if (!guestsPicker || !guestsTrigger || !guestsMenu) return;

        const closeGuestsPicker = () => {
            guestsPicker.classList.remove('is-open');
            guestsTrigger.setAttribute('aria-expanded', 'false');
        };
        const openGuestsPicker = () => {
            guestsPicker.classList.add('is-open');
            guestsTrigger.setAttribute('aria-expanded', 'true');
        };

        guestsTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            if (guestsPicker.classList.contains('is-open')) closeGuestsPicker();
            else openGuestsPicker();
        });
        guestsTrigger.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                guestsTrigger.click();
            }
        });

        guestsMenu.addEventListener('click', (e) => {
            e.stopPropagation();
            const btn = e.target.closest('.counter-btn');
            if (!btn) return;
            const field = btn.dataset.counter;
            const delta = asNumber(btn.dataset.delta, 0);
            if (!field || !delta) return;

            const mins = { adults: 1, children: 0, rooms: 1 };
            const maxs = { adults: 9, children: 9, rooms: 9 };
            const targetInput = field === 'adults' ? adultsInput : (field === 'children' ? childrenInput : roomsInput);
            if (!targetInput) return;

            const current = asNumber(targetInput.value, mins[field] ?? 0);
            const next = Math.min(maxs[field] ?? 9, Math.max(mins[field] ?? 0, current + delta));
            targetInput.value = String(next);
            targetInput.dispatchEvent(new Event('change', { bubbles: true }));
            syncGuestTotals();
        });

        document.addEventListener('click', (e) => {
            if (!guestsPicker.contains(e.target)) closeGuestsPicker();
        });
    }

    function calcNights(a, b) {
        const diff = new Date(b) - new Date(a);
        return diff > 0 ? Math.round(diff / 86400000) : 0;
    }

    function calcExtrasTotal(nights) {
        if (!EXTRA_PRICES.length) return 0;
        const adults = Math.max(1, asNumber(adultsInput ? adultsInput.value : 1, 1));
        const children = Math.max(0, asNumber(childrenInput ? childrenInput.value : 0, 0));
        const totalGuests = adults + children;
        let extrasTotal = 0;
        document.querySelectorAll('.extra-price-check:checked').forEach(function (cb) {
            const idx = parseInt(cb.dataset.index, 10);
            const extra = EXTRA_PRICES[idx];
            if (!extra) return;
            let amount = parseFloat(extra.price) || 0;
            if (extra.type === 'per_day') amount *= nights;
            if (parseInt(cb.dataset.perPerson, 10)) amount *= totalGuests;
            extrasTotal += amount;
        });
        return Math.round(extrasTotal);
    }

    function updateBreakdown() {
        if (!roomSelected) { breakdown.style.display = 'none'; return; }
        const nights = calcNights(checkinEl.value, checkoutEl.value);
        if (nights <= 0) { breakdown.style.display = 'none'; return; }

        const subtotal   = currentPricePerNight * nights;
        const tax        = Math.round(subtotal * TAX_RATE);
        const extrasTotal = calcExtrasTotal(nights);
        const total      = subtotal + tax + extrasTotal;

        document.getElementById('nightsPriceLabel').textContent = '$' + currentPricePerNight.toLocaleString();
        document.getElementById('nightsCount').textContent       = nights;
        document.getElementById('subtotalAmount').textContent    = '$' + subtotal.toLocaleString();
        document.getElementById('taxAmount').textContent         = '$' + tax.toLocaleString();
        document.getElementById('totalAmount').textContent       = '$' + total.toLocaleString();

        if (extrasBreakdownRow) {
            if (extrasTotal > 0) {
                document.getElementById('extrasAmount').textContent = '$' + extrasTotal.toLocaleString();
                extrasBreakdownRow.style.display = 'flex';
            } else {
                extrasBreakdownRow.style.display = 'none';
            }
        }

        breakdown.style.display = 'block';
    }

    // Exposed API for room selection (called from detail page JS)
    window.bookingWidget = {
        updatePrice: function (price, roomName, roomId) {
            currentPricePerNight = price;
            roomSelected = true;

            document.getElementById('widgetNoRoom').style.display       = 'none';
            document.getElementById('widgetPriceSection').style.display = 'block';
            document.getElementById('widgetPriceAmount').textContent    = '$' + price.toLocaleString();
            document.getElementById('widgetRoomName').textContent       = roomName;
            document.getElementById('selectedRoomId').value             = roomId || '';

            if (extraPriceSection) extraPriceSection.style.display = 'block';

            updateBreakdown();
        }
    };

    // Recalculate breakdown whenever an extra-price checkbox changes
    document.querySelectorAll('.extra-price-check').forEach(function (cb) {
        cb.addEventListener('change', updateBreakdown);
    });

    checkinEl.addEventListener('change', function () {
        if (checkoutEl.value && checkoutEl.value <= this.value) {
            const next = new Date(this.value);
            next.setDate(next.getDate() + 1);
            checkoutEl.value = next.toISOString().split('T')[0];
        }
        checkoutEl.min = this.value;
        updateBreakdown();
    });

    checkoutEl.addEventListener('change', updateBreakdown);
    setupGuestsPicker();
    syncGuestTotals();
    if (checkinEl.value && checkoutEl.value) updateBreakdown();

    // ── Reserve Now: pre-submit guard ─────────────────────────────
    document.getElementById('bookingForm').addEventListener('submit', function (e) {
        const roomId  = document.getElementById('selectedRoomId').value;
        const checkin = checkinEl.value;
        const checkout = checkoutEl.value;
        syncGuestTotals();

        if (!roomId) {
            e.preventDefault();
            alert('Please select a room first.');
            document.getElementById('availableRoomsSection')?.scrollIntoView({ behavior: 'smooth' });
            return;
        }
        if (!checkin || !checkout) {
            e.preventDefault();
            alert('Please select your check-in and check-out dates.');
            return;
        }
    });
})();
</script>
@endpush
