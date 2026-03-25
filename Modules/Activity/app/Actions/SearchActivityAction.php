<?php

namespace Modules\Activity\Actions;

use Modules\Activity\DTOs\SearchActivityDto;
use Modules\Activity\Enums\ActivityProviderEnum;
use Modules\Activity\Providers\ActivityProviderInterface;
use Modules\Activity\Providers\Local\LocalActivityProvider;

class SearchActivityAction
{
    /**
     * @return array{offers: \Modules\Activity\DTOs\ActivityOfferDto[], pagination: array}
     */
    public function handle(SearchActivityDto $dto): array
    {
        $providers = $dto->provider
            ? [$dto->provider]
            : ActivityProviderEnum::cases();

        $allOffers = [];
        $pagination = null;

        foreach ($providers as $providerEnum) {
            try {
                $result = $this->resolveProvider($providerEnum)->search($dto);

                array_push($allOffers, ...$result['offers']);

                // Use pagination from the first provider that returns results
                if ($pagination === null && ! empty($result['pagination'])) {
                    $pagination = $result['pagination'];
                }
            } catch (\Throwable) {
                // skip failed providers so other results still return
            }
        }

        return [
            'offers'     => $allOffers,
            'pagination' => $pagination ?? ['current_page' => 1, 'last_page' => 1, 'per_page' => $dto->perPage, 'total' => 0],
        ];
    }

    private function resolveProvider(ActivityProviderEnum $provider): ActivityProviderInterface
    {
        return match ($provider) {
            ActivityProviderEnum::Local => new LocalActivityProvider(),
        };
    }
}
