@php
    /*
     * Sidebar nav definition.
     * Labels use translation keys — __('admin.nav_*')
     */
    $nav = [
        [
            'route'   => 'admin.dashboard',
            'label'   => __('admin.nav_dashboard'),
            'has_sub' => false,
            'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
        ],
        [
            'route'   => 'admin.locations.index',
            'label'   => __('admin.nav_location'),
            'has_sub' => true,
            'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>',
        ],
        [
            'route'   => 'admin.hotels.index',
            'label'   => __('admin.nav_hotel'),
            'has_sub' => true,
            'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
        ],
        [
            'route'   => 'admin.hotel-rooms.index',
            'label'   => __('admin.nav_hotel_rooms'),
            'has_sub' => true,
            'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M5 7l1 12h12l1-12M9 11v4m6-4v4M8 7V5a1 1 0 011-1h6a1 1 0 011 1v2"/>',
        ],
        [
            'route'   => 'admin.tours.index',
            'label'   => __('admin.nav_tour'),
            'has_sub' => true,
            'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>',
        ],
        [
            'route'   => 'admin.flights.index',
            'label'   => __('admin.nav_flight'),
            'has_sub' => true,
            'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>',
        ],
        [
            'route'   => 'admin.bookings.index',
            'label'   => __('admin.nav_bookings'),
            'has_sub' => false,
            'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>',
        ],
        [
            'route'   => 'admin.reviews.index',
            'label'   => __('admin.nav_reviews'),
            'has_sub' => false,
            'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>',
        ],

        [
            'route'   => 'admin.vendor-requests.index',
            'label'   => 'Vendor Requests',
            'has_sub' => false,
            'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
        ],

        ['section' => __('admin.section_content')],

        [
            'route'   => 'admin.news.index',
            'label'   => __('admin.nav_news'),
            'has_sub' => true,
            'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>',
        ],
        [
            'route'   => 'admin.media.index',
            'label'   => __('admin.nav_media'),
            'has_sub' => false,
            'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
        ],
        [
            'route'   => 'admin.amenities.index',
            'label'   => __('admin.nav_amenities'),
            'has_sub' => false,
            'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        ],
        [
            'route'   => 'admin.services.index',
            'label'   => __('admin.nav_services'),
            'has_sub' => false,
            'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5-6h3m-9 9h10a2 2 0 002-2V8a2 2 0 00-2-2H7a2 2 0 00-2 2v8a2 2 0 002 2z"/>',
        ],
        [
            'route'   => 'admin.currencies.index',
            'label'   => __('admin.nav_currencies'),
            'has_sub' => false,
            'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 .895-4 2s1.79 2 4 2 4 .895 4 2-1.79 2-4 2m0-10V6m0 12v-2m0-8h.01M12 12h.01M12 16h.01"/>',
        ],
    ];
@endphp

@foreach ($nav as $item)

    @if (isset($item['section']))
        {{-- Section divider label --}}
        <p class="nav-section text-uppercase fw-semibold ps-3 pt-4 pb-1 mb-0" style="font-size:10px;letter-spacing:.1em;">{{ $item['section'] }}</p>

    @else
        @php $active = request()->routeIs(str_replace('.index', '.*', $item['route'])); @endphp

        @if (Route::has($item['route']))
            <a href="{{ route($item['route']) }}"
               class="sidebar-nav-link sidebar-inactive mb-0
                      {{ $active ? 'sidebar-active' : 'sidebar-hover' }}"
               style="color:{{ $active ? 'var(--clr-sidebar-text-act)' : 'var(--clr-sidebar-text)' }}; text-decoration:none;">
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
            {{-- Route doesn't exist yet — rendered as a disabled span --}}
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
