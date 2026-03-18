<?php

namespace Modules\Hotel\Actions;

use Illuminate\Support\Facades\Log;
use Modules\Hotel\DTOs\SearchHotelDto;
use Modules\Hotel\Enums\HotelProviderEnum;
use Modules\Hotel\Providers\HotelProviderInterface;
use Modules\Hotel\Providers\Local\LocalHotelProvider;
use Modules\Hotel\Providers\TravolyoB2BBaseHotelProvider;
use Modules\Hotel\Providers\Hyperguest\HyperguestHotelProvider;

class SearchHotelAction
{
    private const OVERALL_TIME_BUDGET_SECONDS = 40;

    /** @return \Modules\Hotel\DTOs\HotelOfferDto[] */
    public function handle(SearchHotelDto $dto): array
    {
        $providers = $dto->provider
            ? [$dto->provider]
            : [
                HotelProviderEnum::Local,
                HotelProviderEnum::TravolyoB2BLocal,
                HotelProviderEnum::TravolyoB2BNetStreaming,
                HotelProviderEnum::TravolyoB2BTassPro,
            ];

        $results = [];
        $startedAt = microtime(true);

        foreach ($providers as $providerEnum) {
            if ((microtime(true) - $startedAt) >= self::OVERALL_TIME_BUDGET_SECONDS) {
                Log::warning('Hotel search stopped after hitting overall time budget', [
                    'destination' => $dto->destination,
                    'providers_completed' => array_map(fn ($offer) => $offer->provider->value ?? 'unknown', $results),
                ]);
                break;
            }

            try {
                $providerDto = new SearchHotelDto(
                    destination:$dto->destination,
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
            } catch (\Throwable $e) {
                Log::warning('Hotel provider search skipped after failure', [
                    'provider' => $providerEnum->value,
                    'destination' => $dto->destination,
                    'message' => $e->getMessage(),
                ]);
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
