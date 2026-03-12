<?php

namespace Modules\Flight\Actions;

use Modules\Flight\DTOs\SearchFlightDto;
use Modules\Flight\Enums\FlightProviderEnum;
use Modules\Flight\Exceptions\FlightException;
use Modules\Flight\Providers\Duffel\DuffelProvider;
use Modules\Flight\Providers\FlightProviderInterface;
use Modules\Flight\Providers\TravolyoB2BXmlAgency\TravolyoB2BXmlAgencyProvider;

class SearchFlightAction
{
    /** @return \Modules\Flight\DTOs\FlightOfferDto[] */
    public function handle(SearchFlightDto $dto): array
    {
        $providers = $dto->provider
            ? [$dto->provider]
            : FlightProviderEnum::cases();

        $results = [];

        foreach ($providers as $providerEnum) {
            try {
                $providerDto = new SearchFlightDto(
                    origin:        $dto->origin,
                    destination:   $dto->destination,
                    departureDate: $dto->departureDate,
                    adults:        $dto->adults,
                    cabinClass:    $dto->cabinClass,
                    provider:      $providerEnum,
                    returnDate:    $dto->returnDate,
                    children:      $dto->children,
                    infants:       $dto->infants,
                    currency:      $dto->currency,
                );

                $offers = $this->resolveProvider($providerEnum)->search($providerDto);
                array_push($results, ...$offers);
            } catch (\Throwable) {
                // skip failed providers so the other results still return
            }
        }

        return $results;
    }

    private function resolveProvider(FlightProviderEnum $provider): FlightProviderInterface
    {
        return match ($provider) {
            FlightProviderEnum::Duffel               => new DuffelProvider(),
            FlightProviderEnum::TravolyoB2BXmlAgency => new TravolyoB2BXmlAgencyProvider(),
        };
    }
}
