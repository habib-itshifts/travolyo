<?php

namespace Modules\Hotel\Actions;

use Illuminate\Support\Facades\Log;
use Modules\Hotel\DTOs\SearchHotelDto;
use Modules\Hotel\Enums\HotelProviderEnum;
use Modules\Hotel\Providers\HotelProviderInterface;
use Modules\Hotel\Providers\Hyperguest\HyperguestHotelProvider;
use Modules\Hotel\Providers\Local\LocalHotelProvider;
use Modules\Hotel\Providers\TravolyoB2B\TravolyoB2BHotelProvider;

class SearchHotelAction
{
    /** @return \Modules\Hotel\DTOs\HotelOfferDto[] */
    public function handle(SearchHotelDto $dto): array
    {
        $providers = $this->resolveProviders($dto);
        $results = [];
        $startedAt = microtime(true);
        $overallBudget = max(5, (int) config('hotel.search.overall_timeout', 25));

        $this->extendExecutionTimeLimit($overallBudget + 10);

        foreach ($providers as $providerEnum) {
            if ((microtime(true) - $startedAt) >= $overallBudget) {
                Log::warning('Hotel search overall time budget reached. Remaining providers skipped.', [
                    'destination' => $dto->destination,
                    'provider' => $providerEnum->value,
                    'budget_seconds' => $overallBudget,
                ]);

                break;
            }

            try {
                $providerDto = new SearchHotelDto(
                    destination:       $dto->destination,
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
                Log::warning('Hotel provider search failed and was skipped.', [
                    'provider' => $providerEnum->value,
                    'destination' => $dto->destination,
                    'message' => $e->getMessage(),
                ]);
                report($e);
            }
        }

        return $results;
    }

    /**
     * @return HotelProviderEnum[]
     */
    private function resolveProviders(SearchHotelDto $dto): array
    {
        $enabledProviders = $this->enabledProviders();

        if ($dto->provider !== null) {
            return in_array($dto->provider, $enabledProviders, true)
                ? [$dto->provider]
                : [];
        }

        $configuredProviders = (array) config('hotel.search.default_providers', [
            HotelProviderEnum::Local->value,
            HotelProviderEnum::TravolyoB2B->value,
        ]);

        $providers = [];

        foreach ($configuredProviders as $provider) {
            try {
                $enum = HotelProviderEnum::from((string) $provider);
            } catch (\ValueError) {
                continue;
            }

            if (in_array($enum, $enabledProviders, true)) {
                $providers[$enum->value] = $enum;
            }
        }

        if ($providers !== []) {
            return array_values($providers);
        }

        return $enabledProviders;
    }

    /**
     * @return HotelProviderEnum[]
     */
    private function enabledProviders(): array
    {
        $enabledFlags = (array) config('hotel.search.enabled_providers', []);
        $providers = [];

        foreach ($enabledFlags as $provider => $enabled) {
            if (! $enabled) {
                continue;
            }

            try {
                $enum = HotelProviderEnum::from((string) $provider);
            } catch (\ValueError) {
                continue;
            }

            $providers[$enum->value] = $enum;
        }

        if ($providers !== []) {
            return array_values($providers);
        }

        return [HotelProviderEnum::Local];
    }

    private function resolveProvider(HotelProviderEnum $provider): HotelProviderInterface
    {
        return match ($provider) {
            HotelProviderEnum::Local       => new LocalHotelProvider(),
            HotelProviderEnum::TravolyoB2B => new TravolyoB2BHotelProvider(),
            HotelProviderEnum::Hyperguest  => new HyperguestHotelProvider(),
        };
    }

    private function extendExecutionTimeLimit(int $seconds): void
    {
        if (! function_exists('set_time_limit')) {
            return;
        }

        @set_time_limit(max(30, $seconds));
    }
}
