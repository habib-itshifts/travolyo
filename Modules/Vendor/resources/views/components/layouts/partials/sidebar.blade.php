@php
    $navItems = [
        ['route' => 'vendor.dashboard',         'label' => 'Dashboard',  'icon' => '⊞'],
        ['section' => 'My Listings'],
        ['route' => 'vendor.tours.index',       'label' => 'Tours',      'icon' => '🗺️'],
        ['route' => 'vendor.hotels.index',      'label' => 'Hotels',     'icon' => '🏨'],
        ['route' => 'vendor.flights.index',     'label' => 'Flights',    'icon' => '✈️'],
        ['section' => 'Bookings'],
        ['route' => 'vendor.bookings.index',    'label' => 'Bookings',   'icon' => '📋'],
        ['route' => 'vendor.payments.index',    'label' => 'Earnings',   'icon' => '💰'],
        ['section' => 'Account'],
        ['route' => 'vendor.profile.edit',      'label' => 'Profile',    'icon' => '👤'],
        ['route' => 'vendor.settings.index',    'label' => 'Settings',   'icon' => '⚙️'],
    ];
@endphp

@foreach ($navItems as $item)
    @if (isset($item['section']))
        <p class="mt-5 mb-1 px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
            {{ $item['section'] }}
        </p>
    @else
        @php $active = request()->routeIs($item['route'] . '*'); @endphp
        @if (Route::has($item['route']))
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition
                      {{ $active ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <span>{{ $item['icon'] }}</span>
                <span>{{ $item['label'] }}</span>
            </a>
        @else
            <span class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium text-gray-400 cursor-not-allowed">
                <span>{{ $item['icon'] }}</span>
                <span>{{ $item['label'] }}</span>
            </span>
        @endif
    @endif
@endforeach
