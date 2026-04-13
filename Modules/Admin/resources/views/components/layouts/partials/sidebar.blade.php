@php
    $hotelChildren = [
        [
            'route' => 'admin.hotels.index',
            'label' => __('admin.nav_hotel'),
            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
        ],
        [
            'route' => 'admin.hotel-rooms.index',
            'label' => __('admin.nav_hotel_rooms'),
            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M5 7l1 12h12l1-12M9 11v4m6-4v4M8 7V5a1 1 0 011-1h6a1 1 0 011 1v2"/>',
        ],
        [
            'route' => 'admin.room-types.index',
            'label' => 'Room Types',
            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>',
        ],
        [
            'route' => 'admin.hotels.scraping.create',
            'label' => 'Scrape Hotel',
            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5-6h3m-9 9h10a2 2 0 002-2V8a2 2 0 00-2-2H7a2 2 0 00-2 2v8a2 2 0 002 2z"/>',
        ],
        [
            'route' => 'admin.amenities.index',
            'label' => __('admin.nav_amenities'),
            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        ],
        [
            'route' => 'admin.services.index',
            'label' => __('admin.nav_services'),
            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5-6h3m-9 9h10a2 2 0 002-2V8a2 2 0 00-2-2H7a2 2 0 00-2 2v8a2 2 0 002 2z"/>',
        ],
        [
            'route' => 'admin.promo-codes.index',
            'label' => 'Promo Codes',
            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M3 11l8.586 8.586a2 2 0 002.828 0l6.172-6.172a2 2 0 000-2.828L12 2H5a2 2 0 00-2 2v7z"/>',
        ],
        [
            'route' => 'admin.hotel-deal-supplements.index',
            'label' => 'Deal Supplements',
            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        ],
        [
            'route' => 'admin.currencies.index',
            'label' => __('admin.nav_currencies'),
            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 .895-4 2s1.79 2 4 2 4 .895 4 2-1.79 2-4 2m0-10V6m0 12v-2m0-8h.01M12 12h.01M12 16h.01"/>',
        ],
    ];

    $blogChildren = [
        [
            'route' => 'admin.blogs.index',
            'label' => 'Blogs',
            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6M9 16h6M9 8h6M5 5h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z"/>',
        ],
        [
            'route' => 'admin.blog-categories.index',
            'label' => 'Blog Categories',
            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h7"/>',
        ],
        [
            'route' => 'admin.blog-tags.index',
            'label' => 'Blog Tags',
            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M3 11l8.586 8.586a2 2 0 002.828 0l6.172-6.172a2 2 0 000-2.828L12 2H5a2 2 0 00-2 2v7z"/>',
        ],
    ];

    $usersChildren = [
        [
            'route' => 'admin.vendors.index',
            'label' => 'Vendors',
            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
        ],
        [
            'route' => 'admin.customers.index',
            'label' => 'Customers',
            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
        ],
    ];

    $activityChildren = [
        [
            'route' => 'admin.activities.index',
            'label' => 'All Activities',
            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5v14"/>',
        ],
        [
            'route' => 'admin.activities.create',
            'label' => 'Add new Activity',
            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>',
        ],
    ];

    $spaceChildren = [
        [
            'route' => 'admin.spaces.index',
            'label' => 'All Spaces',
            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
        ],
        [
            'route' => 'admin.spaces.create',
            'label' => 'Add New Space',
            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>',
        ],
    ];

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
            'route'   => 'admin.hotels.group',
            'label'   => __('admin.nav_hotel'),
            'has_sub' => true,
            'children'=> $hotelChildren,
            'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
        ],
        [
            'route'   => 'admin.spaces.group',
            'label'   => 'Spaces',
            'has_sub' => true,
            'children'=> $spaceChildren,
            'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
        ],
        [
            'route'   => 'admin.tours.index',
            'label'   => __('admin.nav_tour'),
            'has_sub' => true,
            'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>',
        ],
        [
            'route'   => 'admin.activities.group',
            'label'   => 'Activities',
            'has_sub' => true,
            'children'=> $activityChildren,
            'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8M8 12h8M8 17h8M5 7h.01M5 12h.01M5 17h.01"/>',
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

        ['section' => 'USERS'],
        [
            'route'   => 'admin.users.group',
            'label'   => 'Users',
            'has_sub' => true,
            'children'=> $usersChildren,
            'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>',
        ],

        ['section' => __('admin.section_content')],
        [
            'route'   => 'admin.blogs.group',
            'label'   => 'Blogs',
            'has_sub' => true,
            'children'=> $blogChildren,
            'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>',
        ],
        [
            'route'   => 'admin.media.index',
            'label'   => __('admin.nav_media'),
            'has_sub' => false,
            'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
        ],
        [
            'route'   => 'admin.destinations.index',
            'label'   => 'Destinations',
            'has_sub' => false,
            'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>',
        ],
    ];
@endphp

@once
    <style>
        .sidebar-nav-toggle {
            width: 100%;
            background: transparent;
            border: 0;
            text-align: left;
        }
        .sidebar-nav-toggle .sidebar-chevron {
            transition: transform .18s ease;
        }
        .sidebar-nav-toggle[aria-expanded="true"] .sidebar-chevron {
            transform: rotate(90deg);
        }
        .sidebar-submenu {
            margin: 0.35rem 0 0.35rem 1rem;
            padding-left: 0.75rem;
            border-left: 1px solid rgba(255, 255, 255, 0.08);
        }
        .sidebar-submenu-link {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.6rem 0.75rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-size: 13px;
            color: var(--clr-sidebar-text);
            transition: background-color .15s ease, color .15s ease;
        }
        .sidebar-submenu-link:hover {
            background: var(--clr-sidebar-hover, rgba(255, 255, 255, 0.06));
            color: var(--clr-sidebar-text-act);
        }
        .sidebar-submenu-link.active {
            background: rgba(58, 181, 212, 0.12);
            color: var(--clr-sidebar-text-act);
        }
    </style>
@endonce

@foreach ($nav as $item)
    @if (isset($item['section']))
        <p class="nav-section text-uppercase fw-semibold ps-3 pt-4 pb-1 mb-0" style="font-size:10px;letter-spacing:.1em;">{{ $item['section'] }}</p>
    @else
        @php
            $children = $item['children'] ?? [];
            $active = !empty($children)
                ? collect($children)->contains(fn ($child) => request()->routeIs(str_replace('.index', '.*', $child['route'])))
                : request()->routeIs(str_replace('.index', '.*', $item['route']));
        @endphp

        @if (!empty($children))
            @php $collapseId = 'sidebar-collapse-' . \Illuminate\Support\Str::slug($item['label']); @endphp
            <button type="button"
                    class="sidebar-nav-link sidebar-nav-toggle sidebar-inactive mb-0 {{ $active ? 'sidebar-active' : 'sidebar-hover' }}"
                    style="color:{{ $active ? 'var(--clr-sidebar-text-act)' : 'var(--clr-sidebar-text)' }};"
                    data-bs-toggle="collapse"
                    data-bs-target="#{{ $collapseId }}"
                    aria-expanded="{{ $active ? 'true' : 'false' }}"
                    aria-controls="{{ $collapseId }}">
                <span class="d-flex align-items-center gap-3">
                    <svg style="width:17px;height:17px;flex-shrink:0;color:{{ $active ? 'var(--clr-primary)' : 'var(--clr-sidebar-icon)' }};"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $item['icon'] !!}
                    </svg>
                    <span>{{ $item['label'] }}</span>
                </span>
                <svg class="sidebar-chevron"
                     style="width:12px;height:12px;color:{{ $active ? 'var(--clr-primary)' : 'var(--clr-sidebar-disabled)' }};"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            <div id="{{ $collapseId }}" class="collapse {{ $active ? 'show' : '' }}">
                <div class="sidebar-submenu">
                    @foreach ($children as $child)
                        @php $childActive = request()->routeIs(str_replace('.index', '.*', $child['route'])); @endphp
                        <a href="{{ route($child['route']) }}" class="sidebar-submenu-link {{ $childActive ? 'active' : '' }}">
                            <svg style="width:15px;height:15px;flex-shrink:0;color:{{ $childActive ? 'var(--clr-primary)' : 'var(--clr-sidebar-icon)' }};"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                {!! $child['icon'] !!}
                            </svg>
                            <span>{{ $child['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @elseif (Route::has($item['route']))
            <a href="{{ route($item['route']) }}"
               class="sidebar-nav-link sidebar-inactive mb-0 {{ $active ? 'sidebar-active' : 'sidebar-hover' }}"
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
