<?php

namespace Modules\Space\Actions;

use Carbon\Carbon;
use Modules\Space\DTOs\SearchSpaceDto;
use Modules\Space\DTOs\SpaceOfferDto;
use Modules\Space\Models\Space;

class SearchSpaceAction
{
    /** @return SpaceOfferDto[] */
    public function handle(SearchSpaceDto $dto): array
    {
        $query = Space::query()
            ->active()
            ->with('amenities')
            ->where(function ($q) use ($dto) {
                $q->where('city', 'like', '%' . $dto->destination . '%')
                  ->orWhere('state', 'like', '%' . $dto->destination . '%')
                  ->orWhere('country', 'like', '%' . $dto->destination . '%')
                  ->orWhere('address', 'like', '%' . $dto->destination . '%');
            })
            ->where('max_guests', '>=', $dto->guests);

        if ($dto->type) {
            $query->where('type', $dto->type);
        }

        if ($dto->bedrooms) {
            $query->where('bedrooms', '>=', $dto->bedrooms);
        }

        if ($dto->bathrooms) {
            $query->where('bathrooms', '>=', $dto->bathrooms);
        }

        if ($dto->priceMin) {
            $query->where(function ($q) use ($dto) {
                $q->where('sale_price', '>=', $dto->priceMin)
                  ->orWhere(function ($q2) use ($dto) {
                      $q2->whereNull('sale_price')->where('price_per_night', '>=', $dto->priceMin);
                  });
            });
        }

        if ($dto->priceMax) {
            $query->where(function ($q) use ($dto) {
                $q->where('sale_price', '<=', $dto->priceMax)
                  ->orWhere(function ($q2) use ($dto) {
                      $q2->whereNull('sale_price')->where('price_per_night', '<=', $dto->priceMax);
                  });
            });
        }

        if (! empty($dto->amenities)) {
            $query->whereHas('amenities', function ($q) use ($dto) {
                $q->whereIn('amenities.id', $dto->amenities);
            }, '>=', count($dto->amenities));
        }

        $spaces = $query->get();

        $nights = $dto->nights();
        $userCurrency = $dto->currency;

        $offers = [];

        foreach ($spaces as $space) {
            if (! $space->isAvailableForDates($dto->checkIn, $dto->checkOut)) {
                continue;
            }

            $pricePerNight = (float) ($space->sale_price ?: $space->price_per_night);
            $cleaningFee   = (float) ($space->cleaning_fee ?? 0);
            $serviceFee    = (float) ($space->service_fee ?? 0);
            $totalPrice    = ($pricePerNight * $nights) + $cleaningFee + $serviceFee;

            $baseCurrency = $space->currency ?: 'USD';
            $convertedPrice = ($userCurrency !== $baseCurrency)
                ? currency($pricePerNight, $baseCurrency, $userCurrency, false)
                : $pricePerNight;
            $convertedTotal = ($userCurrency !== $baseCurrency)
                ? currency($totalPrice, $baseCurrency, $userCurrency, false)
                : $totalPrice;

            $images = [];
            if ($space->featured_image_url) {
                $images[] = $space->featured_image_url;
            }
            $images = array_merge($images, $space->gallery_urls ?? []);

            $offers[] = new SpaceOfferDto(
                id:                     $space->id,
                name:                   $space->name,
                slug:                   $space->slug,
                type:                   $space->type,
                city:                   $space->city ?? '',
                country:                $space->country ?? '',
                address:                $space->address,
                description:            $space->description,
                shortDescription:       $space->short_description,
                maxGuests:              $space->max_guests,
                bedrooms:               $space->bedrooms,
                bathrooms:              $space->bathrooms,
                beds:                   $space->beds,
                checkInTime:            $space->check_in_time,
                checkOutTime:           $space->check_out_time,
                latitude:               $space->latitude ? (float) $space->latitude : null,
                longitude:              $space->longitude ? (float) $space->longitude : null,
                images:                 $images,
                amenityNames:           $space->amenities->pluck('name')->all(),
                basePricePerNight:      $pricePerNight,
                baseCurrency:           $baseCurrency,
                convertedPricePerNight: (float) $convertedPrice,
                convertedCurrency:      $userCurrency,
                cleaningFee:            $cleaningFee,
                serviceFee:             $serviceFee,
                baseTotalPrice:         $totalPrice,
                convertedTotalPrice:    (float) $convertedTotal,
                isDiscounted:           $space->sale_price && $space->sale_price < $space->price_per_night,
            );
        }

        // Sort results
        usort($offers, match ($dto->sortBy) {
            'price_desc' => fn ($a, $b) => $b->basePricePerNight <=> $a->basePricePerNight,
            'newest'     => fn ($a, $b) => $b->id <=> $a->id,
            default      => fn ($a, $b) => $a->basePricePerNight <=> $b->basePricePerNight,
        });

        return $offers;
    }
}
