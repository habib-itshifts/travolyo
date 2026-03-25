<?php

namespace Modules\Activity\Providers;

use Modules\Activity\DTOs\ActivityOfferDto;
use Modules\Activity\DTOs\ActivityOrderDto;
use Modules\Activity\DTOs\PrebookActivityDto;
use Modules\Activity\DTOs\SearchActivityDto;

interface ActivityProviderInterface
{
    /** Search available activities. @return ActivityOfferDto[] */
    public function search(SearchActivityDto $dto): array;

    /** Get full details for a single activity offer. */
    public function getDetails(string $offerId): ActivityOfferDto;

    /** Lock an activity slot before payment. */
    public function prebook(PrebookActivityDto $dto): ActivityOfferDto;

    /** Retrieve an existing activity order/booking. */
    public function getOrder(string $orderId): ActivityOrderDto;

    /** Cancel an existing activity order. */
    public function cancelOrder(string $orderId): bool;
}
