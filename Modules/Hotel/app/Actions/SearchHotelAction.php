<?php

namespace Modules\Hotel\Actions;

use Modules\Hotel\DTOs\SearchHotelDto;
use Modules\Hotel\Enums\HotelProviderEnum;
use Modules\Hotel\Providers\HotelProviderFactory;

class SearchHotelAction
{
    /** @return \Modules\Hotel\DTOs\HotelOfferDto[] */
    public function handle(SearchHotelDto $dto): array
    {
        $providers = $dto->provider
            ? [$dto->provider]
            : [HotelProviderEnum::Local, HotelProviderEnum::TravolyoB2B, HotelProviderEnum::Hyperguest];

        $results = [];

        foreach ($providers as $providerEnum) {
            try {
                $offers = HotelProviderFactory::make($providerEnum)->search($dto);
                array_push($results, ...$offers);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return $results;
    }
}
