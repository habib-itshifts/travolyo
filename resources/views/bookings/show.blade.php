<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking {{ $booking->code }} — Travolyo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">

<div class="max-w-2xl mx-auto py-12 px-4">

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="mb-6 rounded-lg bg-green-50 border border-green-200 p-4 text-green-800">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4 text-red-800">
            {{ session('error') }}
        </div>
    @endif
    @if (session('info'))
        <div class="mb-6 rounded-lg bg-blue-50 border border-blue-200 p-4 text-blue-800">
            {{ session('info') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6 text-white">
            <p class="text-sm opacity-75 uppercase tracking-wide">Booking Reference</p>
            <h1 class="text-3xl font-bold mt-1">{{ $booking->code }}</h1>
        </div>

        {{-- Status badge --}}
        <div class="px-8 py-4 border-b border-gray-100 flex items-center justify-between">
            <span class="text-sm text-gray-500">Status</span>
            @php
                $statusColor = match($booking->status) {
                    'completed', 'confirmed' => 'bg-green-100 text-green-700',
                    'unpaid', 'draft'        => 'bg-yellow-100 text-yellow-700',
                    'cancelled','booking_failed' => 'bg-red-100 text-red-700',
                    default                  => 'bg-gray-100 text-gray-600',
                };
            @endphp
            <span class="px-3 py-1 rounded-full text-sm font-medium {{ $statusColor }}">
                {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
            </span>
        </div>

        {{-- Flight details (if flight booking) --}}
        @if ($booking->object_model === 'flight')
            @php $f = $booking->getJsonMeta('flight_details'); @endphp
            @if (!empty($f))
            <div class="px-8 py-6 border-b border-gray-100">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-400 mb-4">Flight Details</h2>
                <div class="flex items-center gap-6">
                    @if (!empty($f['airline_logo']))
                        <img src="{{ $f['airline_logo'] }}" alt="{{ $f['airline_name'] ?? '' }}" class="h-10 w-10 object-contain">
                    @endif
                    <div>
                        <p class="text-lg font-bold text-gray-900">
                            {{ $f['dep_iata'] ?? '' }} → {{ $f['arr_iata'] ?? '' }}
                        </p>
                        <p class="text-sm text-gray-500 mt-0.5">
                            {{ $f['dep_date'] ?? '' }}
                            @if(!empty($f['dep_time'])) at {{ $f['dep_time'] }} @endif
                            &nbsp;·&nbsp;
                            {{ $f['cabin_class'] ?? '' }}
                        </p>
                    </div>
                </div>
            </div>
            @endif
        @endif

        {{-- Contact --}}
        <div class="px-8 py-6 border-b border-gray-100">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-400 mb-4">Contact</h2>
            <p class="text-gray-800">{{ $booking->first_name }} {{ $booking->last_name }}</p>
            <p class="text-gray-500 text-sm">{{ $booking->email }}</p>
            <p class="text-gray-500 text-sm">{{ $booking->phone }}</p>
        </div>

        {{-- Passengers --}}
        @php $passengers = $booking->getJsonMeta('flight_passengers'); @endphp
        @if (!empty($passengers))
        <div class="px-8 py-6 border-b border-gray-100">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-400 mb-4">Passengers</h2>
            <div class="space-y-3">
                @foreach ($passengers as $i => $pax)
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-800">
                        {{ $pax['title'] ?? '' }} {{ $pax['first_name'] ?? '' }} {{ $pax['last_name'] ?? '' }}
                    </span>
                    <span class="text-gray-400">{{ $pax['nationality'] ?? '' }} · {{ $pax['passport'] ?? '' }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Price --}}
        <div class="px-8 py-6">
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Total</span>
                <span class="text-2xl font-bold text-gray-900">
                    {{ strtoupper($booking->currency) }} {{ number_format($booking->total, 2) }}
                </span>
            </div>
            @if ($booking->paid > 0)
            <div class="flex justify-between items-center mt-2">
                <span class="text-gray-500 text-sm">Paid</span>
                <span class="text-green-600 font-medium">{{ strtoupper($booking->currency) }} {{ number_format($booking->paid, 2) }}</span>
            </div>
            @endif
        </div>

    </div>

    <div class="mt-6 text-center">
        <a href="{{ url('/') }}" class="text-blue-600 hover:underline text-sm">← Back to Home</a>
    </div>

</div>

</body>
</html>
