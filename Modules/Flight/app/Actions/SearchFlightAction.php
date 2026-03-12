<?php

namespace Modules\Flight\Actions;

use Modules\Flight\DTOs\SearchFlightDto;
use Modules\Flight\Enums\FlightProviderEnum;
use Modules\Flight\Exceptions\FlightException;
use Modules\Flight\Providers\Duffel\DuffelProvider;
use Modules\Flight\Providers\FlightProviderInterface;

class SearchFlightAction
{
    /** @return \Modules\Flight\DTOs\FlightOfferDto[] */
    public function handle(SearchFlightDto $dto): array
    {
        return $this->resolveProvider($dto->provider)->search($dto);
    }

    private function resolveProvider(FlightProviderEnum $provider): FlightProviderInterface
    {
        return match ($provider) {
            FlightProviderEnum::Duffel               => new DuffelProvider(),
            FlightProviderEnum::TravolyoB2BXmlAgency => throw new FlightException('TravolyoB2B provider not implemented yet.'),
        };
    }
}
