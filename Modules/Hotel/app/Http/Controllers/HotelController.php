<?php

namespace Modules\Hotel\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class HotelController extends Controller
{
    /**
     * Hotel search/listing page.
     * Actual search is handled by Api\HotelController@search via AJAX.
     */
    public function index(Request $request): View
    {
        $destination = trim((string) $request->query('destination', ''));

        if ($destination !== '' && $request->query('city', '') === '') {
            $resolved = $this->resolveDestinationConfig($destination);

            if ($resolved !== []) {
                $request->merge($resolved);
            }
        }

        $params = [
            'destination' => (string) $request->query('destination', ''),
            'country' => (string) $request->query('country', ''),
            'country_code' => (string) $request->query('country_code', ''),
            'location' => (string) $request->query('location', ''),
            'city' => (string) $request->query('city', ''),
            'search_mode' => (string) $request->query('search_mode', ''),
            'search_cities' => array_values(array_filter(array_map(
                'trim',
                explode('|', (string) $request->query('search_cities', ''))
            ))),
            'check_in' => (string) $request->query('check_in', now()->addDays(4)->toDateString()),
            'check_out' => (string) $request->query('check_out', now()->addDays(8)->toDateString()),
            'adults' => max(1, (int) $request->query('adults', 1)),
            'children' => max(0, (int) $request->query('children', 0)),
            'rooms' => max(1, (int) $request->query('rooms', 1)),
            'provider' => (string) $request->query('provider', ''),
        ];

        $destinationExplorer = $this->loadJsonFile('data/hotel-destination-explorer.json');

        return view('hotel::hotels.index', compact('params', 'destinationExplorer'));
    }

    private function resolveDestinationConfig(string $destination): array
    {
        $payload = $this->loadJsonFile('data/top-cities-to-book.json');
        if ($payload === []) {
            return [];
        }

        $countryTab = collect(Arr::get($payload, 'tabs', []))
            ->first(fn (array $tab) => ($tab['key'] ?? '') === 'countries_and_territories');

        if (! is_array($countryTab)) {
            return [];
        }

        $item = collect(Arr::get($countryTab, 'regions', []))
            ->flatMap(fn (array $region) => Arr::get($region, 'items', []))
            ->first(function (array $item) use ($destination) {
                return strcasecmp((string) ($item['destination'] ?? $item['label'] ?? ''), $destination) === 0
                    || strcasecmp((string) ($item['country'] ?? ''), $destination) === 0
                    || strcasecmp((string) ($item['label'] ?? ''), $destination) === 0;
            });

        if (! is_array($item)) {
            return [];
        }

        return [
            'destination' => (string) ($item['destination'] ?? $item['country'] ?? $item['label'] ?? $destination),
            'country' => (string) ($item['country'] ?? $item['label'] ?? ''),
            'country_code' => (string) ($item['country_code'] ?? ''),
            'location' => '',
            'city' => '',
        ];
    }

    private function loadJsonFile(string $relativePath): array
    {
        $file = public_path($relativePath);

        if (! is_file($file)) {
            return [];
        }

        $payload = json_decode((string) file_get_contents($file), true);

        return is_array($payload) ? $payload : [];
    }

    /**
     * Hotel checkout page loads prebook data from cache via token.
     */
    public function checkout(Request $request): View|RedirectResponse
    {
        $token = $request->get('token');
        $hc = $token ? Cache::get('hotel_checkout_' . $token) : null;

        if (! $hc) {
            return redirect()->route('hotels.index');
        }

        return view('hotel::hotels.checkout', ['hc' => $hc, 'checkout_token' => $token]);
    }

    /**
     * Hotel booking confirmation page.
     */
    public function confirmation(string $code): View|RedirectResponse
    {
        $booking = Booking::where('code', $code)
            ->where('object_model', 'hotel')
            ->first();

        if (! $booking) {
            return redirect()->route('hotels.index');
        }

        $hotelDetails = $booking->getJsonMeta('hotel_details') ?? [];
        $gateway = $booking->getMeta('payment_gateway') ?? '-';

        return view('hotel::hotels.confirmation', compact('booking', 'hotelDetails', 'gateway'));
    }
}
