<?php

namespace Modules\Vendor\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Hotel\Actions\ImportScrapedHotelAction;
use Modules\Hotel\DTOs\ScrapeHotelRequestDto;
use Modules\Hotel\Http\Requests\Vendor\StoreHotelScrapingRequest;
use Throwable;

class HotelScrapingController extends Controller
{
    public function __construct(private readonly ImportScrapedHotelAction $importScrapedHotel) {}

    public function create(): View
    {
        return view('vendor::hotels.scrape');
    }

    public function store(StoreHotelScrapingRequest $request): RedirectResponse
    {
        try {
            $hotel = $this->importScrapedHotel->execute(ScrapeHotelRequestDto::fromArray([
                'url' => $request->validated('url'),
                'actor_id' => auth()->id(),
                'is_vendor' => true,
            ]));
        } catch (Throwable $throwable) {
            report($throwable);

            return back()
                ->withInput()
                ->with('error', $throwable->getMessage() ?: 'Unable to scrape the hotel right now.');
        }

        return redirect()
            ->route('vendor.hotels.edit', $hotel->id)
            ->with('success', 'Hotel "' . $hotel->name . '" imported and saved as draft.');
    }
}
