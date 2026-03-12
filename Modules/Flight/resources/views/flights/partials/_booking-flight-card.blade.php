@php
    $depIata  = strtoupper($f['dep_iata']  ?? '—');
    $arrIata  = strtoupper($f['arr_iata']  ?? '—');
    $depTime  = $f['dep_time']  ?? '';
    $depDate  = $f['dep_date']  ?? '';
    $arrTime  = $f['arr_time']  ?? '';
    $arrDate  = $f['arr_date']  ?? '';
    $duration = $f['duration']  ?? '—';
    $stops    = (int) ($f['stops'] ?? 0);
    $airline  = $f['airline_name'] ?? '—';
    $logo     = $f['airline_logo'] ?? '';
    $cabin    = $f['cabin_class']  ?? 'ECONOMY';
    $trip     = $f['trip_type']    ?? 'one_way';
    $paxCount = max(1, (int)($f['adults'] ?? 1) + (int)($f['children'] ?? 0) + (int)($f['infants'] ?? 0));
@endphp

<div class="detail-card" style="overflow:hidden;">
    {{-- Route banner --}}
    <div class="route-banner">
        <div class="rb-airports">
            <span>{{ $depIata }}</span>
            <div class="rb-sep">
                <div class="rb-sep-line">
                    <span class="rb-sep-plane"><i class="bi bi-airplane-fill"></i></span>
                </div>
            </div>
            <span>{{ $arrIata }}</span>
        </div>
        <div class="rb-meta">
            {{ $depDate }} &nbsp;·&nbsp; {{ $trip === 'round_trip' ? 'Round Trip' : 'One Way' }}
        </div>
    </div>

    {{-- Detail rows --}}
    <div class="detail-card__body">
        <div class="fd-row">
            <div class="fd-icon"><i class="bi bi-clock"></i></div>
            <div>
                <div class="fd-label">Departure</div>
                <div class="fd-value">{{ $depTime }} &nbsp;·&nbsp; {{ $depDate }}</div>
            </div>
        </div>
        <div class="fd-row">
            <div class="fd-icon"><i class="bi bi-clock-history"></i></div>
            <div>
                <div class="fd-label">Arrival</div>
                <div class="fd-value">{{ $arrTime }} &nbsp;·&nbsp; {{ $arrDate }}</div>
            </div>
        </div>
        <div class="fd-row">
            <div class="fd-icon"><i class="bi bi-stopwatch"></i></div>
            <div>
                <div class="fd-label">Duration · Stops</div>
                <div class="fd-value">
                    {{ $duration }} &nbsp;·&nbsp;
                    {{ $stops === 0 ? 'Direct' : $stops . ' stop' . ($stops > 1 ? 's' : '') }}
                </div>
            </div>
        </div>
        <div class="fd-row">
            <div class="fd-icon"><i class="bi bi-airplane"></i></div>
            <div>
                <div class="fd-label">Airline</div>
                <div class="fd-value d-flex align-items-center gap-2">
                    @if($logo)
                        <img src="{{ $logo }}" alt="{{ $airline }}" style="height:20px; object-fit:contain;">
                    @endif
                    {{ $airline }}
                </div>
            </div>
        </div>
        <div class="fd-row">
            <div class="fd-icon"><i class="bi bi-person-check"></i></div>
            <div>
                <div class="fd-label">Passengers · Cabin</div>
                <div class="fd-value">{{ $paxCount }} Pax &nbsp;·&nbsp; {{ $cabin }}</div>
            </div>
        </div>
    </div>
</div>
