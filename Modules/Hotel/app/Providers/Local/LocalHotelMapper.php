<?php

namespace Modules\Hotel\Providers\Local;

use Carbon\Carbon;
use Modules\Hotel\DTOs\HotelOfferDto;
use Modules\Hotel\DTOs\HotelRoomOfferDto;
use Modules\Hotel\Enums\HotelProviderEnum;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelDeal;
use Modules\Hotel\Models\HotelRoom;

class LocalHotelMapper
{
    /**
     * Map a Hotel model to a HotelOfferDto.
     *
     * @param string $checkIn  Guest check-in date (Y-m-d) — needed to match deals
     * @param string $checkOut Guest check-out date (Y-m-d) — needed to match deals
     */
    public function toOfferDto(
        Hotel  $hotel,
        int    $nights,
        string $currency,
        int    $adults           = 1,
        string $checkIn          = '',
        string $checkOut         = '',
    ): HotelOfferDto {
        $images = collect([$hotel->featured_image_url, $hotel->banner_image_url])
            ->merge($hotel->gallery_urls ?? [])
            ->filter()
            ->unique()
            ->values()
            ->all();
        
        $baseCurrency = (string) ($hotel->currency ?: $currency);
        $convertedCurrency = $currency;
        // Build room offer DTOs — each room checks for an applicable deal first
        $rooms = $hotel->rooms
            ->filter(fn (HotelRoom $r) => $r->is_active)
            ->map(fn (HotelRoom $r) => $this->toRoomOfferDto($r, $nights, $baseCurrency, $convertedCurrency, $adults, $checkIn, $checkOut))
            ->values()
            ->all();

        $baseLowestPrice = collect($rooms)
            ->pluck('baseCurrentPrice')
            ->filter(fn ($price) => (float) $price > 0)
            ->min() ?? (float) ($hotel->sale_price ?: $hotel->base_price ?: 0);

        
        $convertedLowestPrice = $baseCurrency === $convertedCurrency
            ? $baseLowestPrice
            : (float) currency($baseLowestPrice, $baseCurrency, $convertedCurrency, false);


        return new HotelOfferDto(
            offerId:          (string) $hotel->id,
            provider:         HotelProviderEnum::Local,
            name:             $hotel->name,
            starRating:       (int) $hotel->star_rating,
            city:             $hotel->city,
            country:          $hotel->country,
            address:          $hotel->address,
            description:      $hotel->description,
            shortDescription: $hotel->short_description,
            checkInTime:      $hotel->check_in_time,
            checkOutTime:     $hotel->check_out_time,
            latitude:         $hotel->latitude ? (float) $hotel->latitude : null,
            longitude:        $hotel->longitude ? (float) $hotel->longitude : null,
            images:           $images,
            amenityNames:     $hotel->amenities->pluck('name')->all(),
            serviceNames:     $hotel->services->pluck('name')->all(),
            baseLowestPrice:      (float)  $baseLowestPrice,
            baseCurrency:         $baseCurrency,
            convertedLowestPrice: (float)  $convertedLowestPrice,
            convertedCurrency:    $currency,
            rooms:            $rooms,
            dbHotelId:        $hotel->id,
            slug:             $hotel->slug,
        );
    }

    /**
     * Map a single HotelRoom to a HotelRoomOfferDto.
     *
     * Pricing priority:
     *   1. Find a published HotelDeal that covers the travel dates → use deal price
     *   2. Fall back to the RoomType base prices (price_sgl_bb / price_dbl_bb)
     *   3. Last resort: hotel-level sale_price / base_price
     */
    public function toRoomOfferDto(
        HotelRoom $room,
        int $nights,
        string $baseCurrency,
        string $convertedCurrency,
        int $adults = 1,
        string $checkIn = '',
        string $checkOut = '',
    ): HotelRoomOfferDto {
        $images = collect([$room->image_url])
            ->merge($room->gallery_urls ?? [])
            ->filter()
            ->unique()
            ->values()
            ->all();

        $roomType = $room->roomType;
        $amenityNames = $roomType?->amenities?->pluck('name')->all() ?? [];


        // Step 1: Find applicable deal
        $deal = $this->findApplicableDeal($room, $checkIn, $checkOut);
        
        // Step 2: Get standard room price first
        $standardPrice = $adults <= 1
            ? (float) $roomType->price_sgl_bb
            : (float) $roomType->price_dbl_bb;


        // Step 3: If deal exists, get deal price
        $dealId = null;
        $originalPrice = $standardPrice; // ← always set original to standard
        $currentPrice = $standardPrice;  // ← default current to standard too

        if ($deal) {
            $dealPrice = $adults <= 1
                ? (float) $deal->price_sgl_bb
                : (float) $deal->price_dbl_bb;

                $dealId = $deal->id;
                $currentPrice = $dealPrice;
        }


        // If no real discount, clear original so isDiscounted() returns false
        if ($currentPrice >= $originalPrice) {
            $originalPrice = 0.0;
        }

        // Step 4: Last resort hotel-level pricing
        if ($currentPrice <= 0) {
            $currentPrice = (float) ($room->hotel?->sale_price ?: $room->hotel?->base_price ?: 0);
            $originalPrice = 0.0;
            $dealId = null;
        }

        // Step 5: Last resort hotel-level pricing
        if ($currentPrice <= 0) {
            $fallbackPrice = (float) ($room->hotel?->sale_price ?: $room->hotel?->base_price ?: 0);
            $currentPrice = $fallbackPrice;
            $originalPrice = 0.0;
            $dealId = null;
        }

        // Converted prices
        $convertedCurrentPrice = currency($currentPrice, $baseCurrency, $convertedCurrency, false);
        $convertedOriginalPrice =currency($originalPrice, $baseCurrency, $convertedCurrency, false);

        // Totals should always use CURRENT price
        $baseTotalPrice = $currentPrice * $nights;
        $convertedTotalPrice = $convertedCurrentPrice * $nights;

        return new HotelRoomOfferDto(
            roomId: (string) $room->id,
            name: $room->display_name,
            roomType: $roomType?->name ?? $room->display_name,
            bedConfiguration: (array) ($roomType?->bed_configuration ?? []),
            maxAdults: (int) ($roomType?->max_adults ?? 2),
            maxChildren: (int) ($roomType?->max_children ?? 0),

            baseOriginalPrice: $originalPrice,
            convertedOriginalPrice: $convertedOriginalPrice,

            baseCurrentPrice: $currentPrice,
            convertedCurrentPrice: $convertedCurrentPrice,

            baseTotalPrice: $baseTotalPrice,
            convertedTotalPrice: $convertedTotalPrice,

            nights: $nights,
            baseCurrency: $baseCurrency,
            convertedCurrency: $convertedCurrency,
            isAvailable: $room->is_active,
            amenityNames: $amenityNames,
            sizeSqm: $roomType?->size_sqm ? (float) $roomType->size_sqm : null,
            viewType: $roomType?->view_type,
            description: $roomType?->description,
            images: $images,
            dealId: $dealId,
        );
    }

    /**
     * Find the best published deal for this room that covers the guest's travel dates.
     *
     * A deal is "applicable" when ALL of these are true:
     *   - status = 'published'
     *   - deal.hotel_id matches the room's hotel
     *   - deal.room_type_id matches the room's room type
     *   - travel_date_start <= checkIn AND travel_date_end >= checkOut
     *   - check-in date is NOT in the deal's blackout_dates
     *   - booking_window is null OR today <= booking_window (still within the booking window)
     *   - release_period is null/0 OR days-until-checkin >= release_period
     *
     * If multiple deals match, the one with the lowest effective price wins.
     */
    private function findApplicableDeal(HotelRoom $room, string $checkIn, string $checkOut): ?HotelDeal
    {
        // No dates provided — can't match any deal
        if (empty($checkIn) || empty($checkOut)) {
            return null;
        }

        $roomType = $room->roomType;
        if (! $roomType) {
            return null;
        }

        $today       = Carbon::today();
        $checkInDate = Carbon::parse($checkIn);

        // Query published deals for this hotel + room type that cover the travel dates
        $deals = HotelDeal::query()
            ->published()
            ->where('hotel_id', $room->hotel_id)
            ->where('room_type_id', $roomType->id)
            ->where('travel_date_start', '<=', $checkIn)
            ->where('travel_date_end', '>=', $checkOut)
            ->get();

        // // Filter out deals that fail runtime checks (blackout, booking window, release period)
        // $applicable = $deals->filter(function (HotelDeal $deal) use ($checkIn, $today, $checkInDate) {
        //     // Blackout check — if check-in falls on a blacked-out date, skip this deal
        //     if ($deal->isBlackedOut($checkIn)) {
        //         return false;
        //     }

        //     // Booking window — if set, today must be on or before the window deadline
        //     if ($deal->booking_window && $today->gt($deal->booking_window)) {
        //         return false;
        //     }

        //     // Release period — must book at least N days before check-in
        //     if ($deal->release_period && $deal->release_period > 0) {
        //         $daysUntilCheckIn = $today->diffInDays($checkInDate, false);
        //         if ($daysUntilCheckIn < $deal->release_period) {
        //             return false;
        //         }
        //     }

        //     return true;
        // });

        return $deals->first();

        if ($applicable->isEmpty()) {
            return null;
        }

        // If multiple deals match, pick the one with the lowest double price (best value)
        // return $applicable->sortBy(fn (HotelDeal $d) => (float) ($d->price_dbl_bb ?: $d->price_sgl_bb ?: PHP_FLOAT_MAX))->first();
    }
}
