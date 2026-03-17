<?php

namespace Modules\Hotel\DTOs;

class ScrapeHotelRequestDto
{
    public function __construct(
        public readonly string $url,
        public readonly int $actorId,
        public readonly bool $isVendor,
        public readonly ?string $status = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            url: trim((string) ($data['url'] ?? '')),
            actorId: (int) ($data['actor_id'] ?? 0),
            isVendor: (bool) ($data['is_vendor'] ?? false),
            status: isset($data['status']) ? (string) $data['status'] : null,
        );
    }
}
