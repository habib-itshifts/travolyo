@php
    $nav = [
        [
            'route'  => 'customer.dashboard',
            'label'  => 'Dashboard',
            'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
        ],
        [
            'route'  => 'customer.bookings.index',
            'label'  => 'My Bookings',
            'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>',
        ],
        [
            'route'  => 'customer.wishlist.index',
            'label'  => 'Wishlist',
            'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>',
        ],
        [
            'route'  => 'customer.profile.edit',
            'label'  => 'Profile',
            'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
        ],
    ];
@endphp

@foreach ($nav as $item)
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
        </span>
    @endif
@endforeach
