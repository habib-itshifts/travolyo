@php
    $nav = [
        [
            'route'   => 'vendor.dashboard',
            'label'   => 'Dashboard',
            'has_sub' => false,
            'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
        ],

        ['section' => 'My Listings'],

        [
            'route'   => 'vendor.tours.index',
            'label'   => 'Tours',
            'has_sub' => true,
            'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>',
        ],
        [
            'route'   => 'vendor.hotels.index',
            'label'   => 'Hotels',
            'has_sub' => true,
            'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
        ],
        [
            'route'   => 'vendor.flights.index',
            'label'   => 'Flights',
            'has_sub' => true,
            'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>',
        ],

        ['section' => 'Bookings'],

        [
            'route'   => 'vendor.bookings.index',
            'label'   => 'Bookings',
            'has_sub' => false,
            'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>',
        ],
        [
            'route'   => 'vendor.payments.index',
            'label'   => 'Earnings',
            'has_sub' => false,
            'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        ],

        ['section' => 'Account'],

        [
            'route'   => 'vendor.profile.edit',
            'label'   => 'Profile',
            'has_sub' => false,
            'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
        ],
        [
            'route'   => 'vendor.settings.index',
            'label'   => 'Settings',
            'has_sub' => false,
            'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
        ],
    ];
@endphp

@foreach ($nav as $item)

    @if (isset($item['section']))
        <p class="nav-section text-uppercase fw-semibold ps-3 pt-4 pb-1 mb-0" style="font-size:10px;letter-spacing:.1em;">{{ $item['section'] }}</p>

    @else
        @php $active = request()->routeIs(str_replace('.index', '.*', $item['route'])); @endphp

        @if (Route::has($item['route']))
            <a href="{{ route($item['route']) }}"
               class="sidebar-nav-link sidebar-inactive mb-0 {{ $active ? 'sidebar-active' : 'sidebar-hover' }}"
               style="color:{{ $active ? 'var(--clr-sidebar-text-act)' : 'var(--clr-sidebar-text)' }};">
                <span class="d-flex align-items-center gap-3">
                    <svg style="width:17px;height:17px;flex-shrink:0;color:{{ $active ? 'var(--clr-primary)' : 'var(--clr-sidebar-icon)' }};"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $item['icon'] !!}
                    </svg>
                    <span>{{ $item['label'] }}</span>
                </span>
                @if ($item['has_sub'])
                    <svg style="width:12px;height:12px;color:{{ $active ? 'var(--clr-primary)' : 'var(--clr-sidebar-disabled)' }};"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                @endif
            </a>
        @else
            <span class="sidebar-nav-link sidebar-inactive mb-0 opacity-25"
                  style="cursor:not-allowed;color:var(--clr-sidebar-disabled);">
                <span class="d-flex align-items-center gap-3">
                    <svg style="width:17px;height:17px;flex-shrink:0;color:var(--clr-sidebar-disabled);"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $item['icon'] !!}
                    </svg>
                    <span>{{ $item['label'] }}</span>
                </span>
                @if ($item['has_sub'])
                    <svg style="width:12px;height:12px;color:var(--clr-sidebar-disabled);"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                @endif
            </span>
        @endif
    @endif

@endforeach
