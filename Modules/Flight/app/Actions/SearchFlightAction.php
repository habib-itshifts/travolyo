<?php

namespace Modules\Flight\Actions;

use Modules\Flight\DTOs\SearchFlightDto;
use Modules\Flight\Enums\FlightProvider;
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

    private function resolveProvider(FlightProvider $provider): FlightProviderInterface
    {
        return match ($provider) {
            FlightProvider::Duffel               => new DuffelProvider(),
            FlightProvider::TravolyoB2BXmlAgency => throw new FlightException('TravolyoB2B provider not implemented yet.'),
        };
    }
}
