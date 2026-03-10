@php
    $navItems = [
        ['route' => 'customer.dashboard',       'label' => 'Dashboard'],
        ['route' => 'customer.bookings.index',  'label' => 'My Bookings'],
        ['route' => 'customer.wishlist.index',  'label' => 'Wishlist'],
        ['route' => 'customer.profile.edit',    'label' => 'Profile'],
    ];
@endphp

@foreach ($navItems as $item)
    @if (Route::has($item['route']))
        <a href="{{ route($item['route']) }}"
           class="text-sm font-medium transition
                  {{ request()->routeIs($item['route'] . '*') ? 'text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-900' }}">
            {{ $item['label'] }}
        </a>
    @else
        <span class="text-sm font-medium text-gray-300 cursor-not-allowed">{{ $item['label'] }}</span>
    @endif
@endforeach
