<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Modules\Hotel\Actions\SearchHotelAction;
use Modules\Hotel\DTOs\SearchHotelDto;
use Modules\Hotel\Resources\HotelOfferResource;

class WebsiteController extends Controller
{
    public function exploreDestinationPublicHotels(Request $request): View
    {
        $destination = trim((string) $request->query('destination', ''));
        $key = trim((string) $request->query('tab', ''));

        $destinationExplorerFile = public_path('data/hotel-destination-explorer.json');
        $destinationExplorer = [];

        if (is_file($destinationExplorerFile)) {
            $decoded = json_decode((string) file_get_contents($destinationExplorerFile), true);
            $destinationExplorer = is_array($decoded) ? $decoded : [];
        }

        $tabs = collect(data_get($destinationExplorer, 'tabs', []))->values();

        $tab = $key !== ''
            ? $tabs->firstWhere('key', $key)
            : $this->resolveTabByDestination($tabs, $destination);

        $tab ??= $tabs->firstWhere('key', 'popular_in_uae') ?? $tabs->first();
        abort_if(! is_array($tab), 404);

        return $this->buildView($tab, $tabs, $destination);
    }

    public function exploreDestinationShow(string $key): View
    {
        $destinationExplorerFile = public_path('data/hotel-destination-explorer.json');
        $destinationExplorer = [];

        if (is_file($destinationExplorerFile)) {
            $decoded = json_decode((string) file_get_contents($destinationExplorerFile), true);
            $destinationExplorer = is_array($decoded) ? $decoded : [];
        }

        $tabs = collect(data_get($destinationExplorer, 'tabs', []))->values();
        $tab = $tabs->firstWhere('key', $key);

        abort_if(! is_array($tab), 404);

        return $this->buildView($tab, $tabs);
    }

    private function buildView(array $tab, \Illuminate\Support\Collection $tabs, string $destination = ''): View
    {
        $searchDestination = $destination !== '' ? $destination : (string) data_get($tab, 'label', '');

        $searchDto = new SearchHotelDto(
            destination: $searchDestination,
            checkIn: now()->addDays(4)->toDateString(),
            checkOut: now()->addDays(8)->toDateString(),
            adults: 1,
            children: 0,
            rooms: 1,
            starRating: null,
            priceMin: null,
            priceMax: null,
            amenityIds: null,
            currency: (string) session('currency', config('currency.default', 'USD')),
            sortBy: 'price_asc',
            perPage: 20,
            page: 1,
            provider: null,
        );

        $offers = (new SearchHotelAction())->handle($searchDto);
        $featuredHotels = collect(HotelOfferResource::collection(collect($offers))->resolve())->values();

        return view('website.explore-destination.index', [
            'tab' => $tab,
            'tabs' => $tabs,
            'featuredHotels' => $featuredHotels,
            'selectedDestination' => $searchDestination,
            'defaultHotelCheckIn' => $searchDto->checkIn,
            'defaultHotelCheckOut' => $searchDto->checkOut,
        ]);
    }

    private function resolveTabByDestination(\Illuminate\Support\Collection $tabs, string $destination): ?array
    {
        if ($destination === '') {
            return null;
        }

        return $tabs->first(function (array $tab) use ($destination) {
            if (strcasecmp((string) data_get($tab, 'label', ''), $destination) === 0) {
                return true;
            }

            return collect(data_get($tab, 'items', []))->contains(function (array $item) use ($destination) {
                return strcasecmp((string) data_get($item, 'label', ''), $destination) === 0
                    || strcasecmp((string) data_get($item, 'city', ''), $destination) === 0
                    || strcasecmp((string) data_get($item, 'destination', ''), $destination) === 0
                    || strcasecmp((string) data_get($item, 'country', ''), $destination) === 0;
            });
        });
    }
}
