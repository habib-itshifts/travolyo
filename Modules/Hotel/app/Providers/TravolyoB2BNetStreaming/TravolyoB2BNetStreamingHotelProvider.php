<?php

namespace Modules\Hotel\Providers\TravolyoB2BNetStreaming;

use Modules\Hotel\DTOs\HotelOfferDto;
use Modules\Hotel\DTOs\HotelOrderDto;
use Modules\Hotel\DTOs\PrebookHotelDto;
use Modules\Hotel\DTOs\SearchHotelDto;
use Modules\Hotel\Providers\HotelProviderInterface;

class TravolyoB2BNetStreamingHotelProvider implements HotelProviderInterface
{
    public function search(SearchHotelDto $dto): array
    {
        // TODO: implement Travolyo B2B Net Streaming hotel search API
        return [];
    }

    public function prebook(PrebookHotelDto $dto): HotelOfferDto
    {
        // TODO: implement Travolyo B2B Net Streaming prebook
        throw new \RuntimeException('TravolyoB2BNetStreaming hotel prebook not yet implemented.');
    }

    public function getOrder(string $orderId): HotelOrderDto
    {
        // TODO: implement
        throw new \RuntimeException('TravolyoB2BNetStreaming hotel getOrder not yet implemented.');
    }

    public function cancelOrder(string $orderId): bool
    {
        // TODO: implement
        throw new \RuntimeException('TravolyoB2BNetStreaming hotel cancelOrder not yet implemented.');
    }
}
