<?php

namespace Modules\Flight\Providers;

use Modules\Flight\DTOs\SearchFlightDto;
use Modules\Flight\DTOs\PrebookFlightDto;
use Modules\Flight\DTOs\CheckoutFlightDto;
use Modules\Flight\DTOs\PayFlightDto;
use Modules\Flight\DTOs\FlightOfferDto;
use Modules\Flight\DTOs\FlightOrderDto;

interface FlightProviderInterface
{
    /** Search available flight offers */
    public function search(SearchFlightDto $dto): array; // FlightOfferDto[]

    /** Hold/prebook a specific offer before payment */
    public function prebook(PrebookFlightDto $dto): FlightOfferDto;

    /** Confirm passenger details before payment */
    public function checkout(CheckoutFlightDto $dto): FlightOfferDto;

    /** Complete payment and create the order */
    public function pay(PayFlightDto $dto): FlightOrderDto;

    /** Retrieve an existing order */
    public function getOrder(string $orderId): FlightOrderDto;

    /** Cancel an existing order */
    public function cancelOrder(string $orderId): bool;
}
