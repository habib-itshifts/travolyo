<?php

namespace Modules\Space\Providers\Local;

use Carbon\Carbon;
use Modules\Space\DTOs\PrebookSpaceDto;
use Modules\Space\DTOs\SearchSpaceDto;
use Modules\Space\DTOs\SpaceOfferDto;
use Modules\Space\Exceptions\SpaceException;
use Modules\Space\Models\Space;
use Modules\Space\Providers\SpaceProviderInterface;

class LocalSpaceProvider implements SpaceProviderInterface
{
    public function __construct(private LocalSpaceMapper $mapper) {}

    // -------------------------------------------------------------------------
    // Search
    // -------------------------------------------------------------------------

    /** @return SpaceOfferDto[] */
    public function search(SearchSpaceDto $dto): array
    {
        $spaces = Space::query()
            ->active()
            ->with('amenities')
            ->where(function ($q) use ($dto) {
                $q->where('city', 'like', '%' . $dto->destination . '%')
                  ->orWhere('state', 'like', '%' . $dto->destination . '%')
                  ->orWhere('country', 'like', '%' . $dto->destination . '%')
                  ->orWhere('address', 'like', '%' . $dto->destination . '%');
            })
            ->where('max_guests', '>=', $dto->guests)
            ->when($dto->type, fn ($q) => $q->where('type', $dto->type))
            ->when($dto->bedrooms, fn ($q) => $q->where('bedrooms', '>=', $dto->bedrooms))
            ->when($dto->bathrooms, fn ($q) => $q->where('bathrooms', '>=', $dto->bathrooms))
            ->when($dto->priceMin, fn ($q) => $q->where(
                fn ($q2) => $q2->where('sale_price', '>=', $dto->priceMin)
                               ->orWhere(fn ($q3) => $q3->whereNull('sale_price')->where('price_per_night', '>=', $dto->priceMin))
            ))
            ->when($dto->priceMax, fn ($q) => $q->where(
                fn ($q2) => $q2->where('sale_price', '<=', $dto->priceMax)
                               ->orWhere(fn ($q3) => $q3->whereNull('sale_price')->where('price_per_night', '<=', $dto->priceMax))
            ))
            ->when(! empty($dto->amenities), fn ($q) => $q->whereHas(
                'amenities',
                fn ($q2) => $q2->whereIn('amenities.name', $dto->amenities),
                '>=',
                count($dto->amenities)
            ))
            ->get();

        $nights = $dto->nights();
        $offers = [];

        foreach ($spaces as $space) {
            if (! $space->isAvailableForDates($dto->checkIn, $dto->checkOut)) {
                continue;
            }

            $offers[] = $this->mapper->toOfferDto($space, $this->calculatePricing(
                space:    $space,
                nights:   $nights,
                currency: $dto->currency,
            ));
        }

        usort($offers, match ($dto->sortBy) {
            'price_desc' => fn ($a, $b) => $b->basePricePerNight <=> $a->basePricePerNight,
            'newest'     => fn ($a, $b) => $b->id <=> $a->id,
            default      => fn ($a, $b) => $a->basePricePerNight <=> $b->basePricePerNight,
        });

        return $offers;
    }

    // -------------------------------------------------------------------------
    // Prebook
    // -------------------------------------------------------------------------

    public function prebook(PrebookSpaceDto $dto): SpaceOfferDto
    {
        $space = Space::with('amenities')->active()->find($dto->spaceId);

        if (! $space) {
            throw SpaceException::notFound($dto->spaceId);
        }

        $checkIn  = Carbon::parse($dto->checkIn);
        $checkOut = Carbon::parse($dto->checkOut);
        $nights   = (int) $checkIn->diffInDays($checkOut);

        if ($checkOut->lte($checkIn)) {
            throw SpaceException::invalidDates();
        }

        if ($space->min_stay_nights && $nights < $space->min_stay_nights) {
            throw SpaceException::minStayNotMet($space->min_stay_nights);
        }

        if ($space->max_stay_nights && $nights > $space->max_stay_nights) {
            throw SpaceException::maxStayExceeded($space->max_stay_nights);
        }

        if (! $space->isAvailableForDates($dto->checkIn, $dto->checkOut)) {
            throw SpaceException::unavailable();
        }

        // Per-date pricing (respects price overrides in availability calendar)
        $totalNightPrice = 0;
        $current = $checkIn->copy();
        while ($current->lt($checkOut)) {
            $totalNightPrice += $space->effectivePriceForDate($current->toDateString());
            $current->addDay();
        }

        return $this->mapper->toOfferDto($space, $this->calculatePricing(
            space:           $space,
            nights:          $nights,
            currency:        $dto->currency,
            totalNightPrice: $totalNightPrice,
        ));
    }

    // -------------------------------------------------------------------------
    // Shared pricing helper
    // -------------------------------------------------------------------------

    private function calculatePricing(Space $space, int $nights, string $currency, ?float $totalNightPrice = null): array
    {
        $pricePerNight = (float) ($space->sale_price ?: $space->price_per_night);
        $total         = $totalNightPrice ?? ($pricePerNight * $nights);
        $cleaningFee   = (float) ($space->cleaning_fee ?? 0);
        $serviceFee    = (float) ($space->service_fee ?? 0);
        $totalPrice    = $total + $cleaningFee + $serviceFee;

        $baseCurrency = $space->currency ?: 'USD';
        $converted    = ($currency !== $baseCurrency);

        return [
            'price_per_night'           => $totalNightPrice ? ($total / max($nights, 1)) : $pricePerNight,
            'base_currency'             => $baseCurrency,
            'converted_price_per_night' => $converted ? (float) currency($pricePerNight, $baseCurrency, $currency, false) : $pricePerNight,
            'converted_currency'        => $currency,
            'cleaning_fee'              => $cleaningFee,
            'service_fee'               => $serviceFee,
            'total_price'               => $totalPrice,
            'converted_total_price'     => $converted ? (float) currency($totalPrice, $baseCurrency, $currency, false) : $totalPrice,
        ];
    }
}
