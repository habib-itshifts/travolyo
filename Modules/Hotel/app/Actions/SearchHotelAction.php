<?php

namespace Modules\Hotel\Actions;

use Modules\Hotel\DTOs\SearchHotelDto;
use Modules\Hotel\Enums\HotelProviderEnum;
use Modules\Hotel\Providers\HotelProviderInterface;
use Modules\Hotel\Providers\Local\LocalHotelProvider;
use Modules\Hotel\Providers\TravolyoB2BBaseHotelProvider;
use Modules\Hotel\Providers\Hyperguest\HyperguestHotelProvider;

class SearchHotelAction
{
    /** @return \Modules\Hotel\DTOs\HotelOfferDto[] */
    public function handle(SearchHotelDto $dto): array
    {
        $providers = $dto->provider
            ? [$dto->provider]
            : HotelProviderEnum::cases();

        $results = [];

        foreach ($providers as $providerEnum) {
            try {
                $providerDto = new SearchHotelDto(
                    city:       $dto->city,
                    checkIn:    $dto->checkIn,
                    checkOut:   $dto->checkOut,
                    adults:     $dto->adults,
                    children:   $dto->children,
                    rooms:      $dto->rooms,
                    starRating: $dto->starRating,
                    priceMin:   $dto->priceMin,
                    priceMax:   $dto->priceMax,
                    amenityIds: $dto->amenityIds,
                    currency:   $dto->currency,
                    sortBy:     $dto->sortBy,
                    perPage:    $dto->perPage,
                    provider:   $providerEnum,
                );

                $offers = $this->resolveProvider($providerEnum)->search($providerDto);
                array_push($results, ...$offers);
            } catch (\Throwable) {
                // skip failed providers so the other results still return
            }
        }

        return $results;
    }

    private function resolveProvider(HotelProviderEnum $provider): HotelProviderInterface
    {
        return match ($provider) {
            HotelProviderEnum::Local       => new LocalHotelProvider(),
            HotelProviderEnum::TravolyoB2B => new TravolyoB2BBaseHotelProvider(),
            HotelProviderEnum::Hyperguest  => new HyperguestHotelProvider(),
        };
    }
}
