@php
    $navItems = [
        ['route' => 'admin.dashboard',       'label' => 'Dashboard',  'icon' => '⊞'],
        ['section' => 'Users & Access'],
        ['route' => 'admin.users.index',     'label' => 'Users',      'icon' => '👤'],
        ['route' => 'admin.vendors.index',   'label' => 'Vendors',    'icon' => '🏪'],
        ['route' => 'admin.roles.index',     'label' => 'Roles',      'icon' => '🔑'],
        ['section' => 'Listings'],
        ['route' => 'admin.tours.index',     'label' => 'Tours',      'icon' => '🗺️'],
        ['route' => 'admin.hotels.index',    'label' => 'Hotels',     'icon' => '🏨'],
        ['route' => 'admin.flights.index',   'label' => 'Flights',    'icon' => '✈️'],
        ['section' => 'Bookings'],
        ['route' => 'admin.bookings.index',  'label' => 'Bookings',   'icon' => '📋'],
        ['route' => 'admin.payments.index',  'label' => 'Payments',   'icon' => '💳'],
        ['section' => 'System'],
        ['route' => 'admin.settings.index',  'label' => 'Settings',   'icon' => '⚙️'],
    ];
@endphp

@foreach ($navItems as $item)
    @if (isset($item['section']))
        <p class="mt-5 mb-1 px-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
            {{ $item['section'] }}
        </p>
    @else
        @php $active = request()->routeIs($item['route'] . '*'); @endphp
        @if (Route::has($item['route']))
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition
                      {{ $active ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                <span>{{ $item['icon'] }}</span>
                <span>{{ $item['label'] }}</span>
            </a>
        @else
            <span class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium text-gray-600 cursor-not-allowed">
                <span>{{ $item['icon'] }}</span>
                <span>{{ $item['label'] }}</span>
            </span>
        @endif
    @endif
@endforeach
