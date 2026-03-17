<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Hotel\Actions\ImportScrapedHotelAction;
use Modules\Hotel\DTOs\ScrapeHotelRequestDto;
use Modules\Hotel\Enums\HotelStatusEnum;
use Modules\Hotel\Http\Requests\Admin\StoreHotelScrapingRequest;
use Throwable;

class HotelScrapingController extends Controller
{
    public function __construct(private readonly ImportScrapedHotelAction $importScrapedHotel) {}

    public function create(): View
    {
        return view('admin::hotels.scrape', [
            'statusOptions' => HotelStatusEnum::cases(),
        ]);
    }

    public function store(StoreHotelScrapingRequest $request): RedirectResponse
    {
        try {
            $hotel = $this->importScrapedHotel->execute(ScrapeHotelRequestDto::fromArray([
                'url' => $request->validated('url'),
                'status' => $request->validated('status'),
                'actor_id' => auth()->id(),
                'is_vendor' => false,
            ]));
        } catch (Throwable $throwable) {
            report($throwable);

            return back()
                ->withInput()
                ->with('error', $throwable->getMessage() ?: 'Unable to scrape the hotel right now.');
        }

        return redirect()
            ->route('admin.hotels.edit', $hotel->id)
            ->with('success', 'Hotel "' . $hotel->name . '" imported successfully.');
    }
}
