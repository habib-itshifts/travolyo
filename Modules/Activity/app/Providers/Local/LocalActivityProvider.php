<?php

namespace Modules\Activity\Providers\Local;

use App\Models\Booking;
use Modules\Activity\DTOs\ActivityOfferDto;
use Modules\Activity\DTOs\ActivityOrderDto;
use Modules\Activity\DTOs\PrebookActivityDto;
use Modules\Activity\DTOs\SearchActivityDto;
use Modules\Activity\Enums\ActivityProviderEnum;
use Modules\Activity\Exceptions\ActivityException;
use Modules\Activity\Models\Activity;
use Modules\Activity\Models\ActivityBookingPassenger;
use Modules\Activity\Providers\ActivityProviderInterface;

class LocalActivityProvider implements ActivityProviderInterface
{
    private LocalActivityMapper $mapper;

    public function __construct()
    {
        $this->mapper = new LocalActivityMapper();
    }

    /** @return ActivityOfferDto[] */
    public function search(SearchActivityDto $dto): array
    {
        $query = Activity::query()
            ->with(['image', 'author'])
            ->published()
            ->orderByDesc('id');

        if ($dto->destination) {
            $query->where(function ($q) use ($dto) {
                $q->where('city', 'like', '%' . $dto->destination . '%')
                  ->orWhere('title', 'like', '%' . $dto->destination . '%')
                  ->orWhere('address', 'like', '%' . $dto->destination . '%');
            });
        }

        if ($dto->category) {
            $query->where('category', $dto->category);
        }

        if ($dto->priceMax !== null) {
            $query->where('price_per_person', '<=', $dto->priceMax);
        }

        if ($dto->priceMin !== null) {
            $query->where('price_per_person', '>=', $dto->priceMin);
        }

        if ($dto->instantConfirmation !== null) {
            $query->where('instant_confirmation', $dto->instantConfirmation);
        }

        // Sorting
        match ($dto->sortBy) {
            'price_asc'  => $query->orderBy('price_per_person'),
            'price_desc' => $query->orderByDesc('price_per_person'),
            default      => null, // keep default ordering (latest)
        };

        $activities = $query->paginate($dto->perPage, ['*'], 'page', $dto->page);

        return [
            'offers' => collect($activities->items())
                ->map(fn (Activity $a) => $this->mapper->toOfferDto($a))
                ->all(),
            'pagination' => [
                'current_page' => $activities->currentPage(),
                'last_page'    => $activities->lastPage(),
                'per_page'     => $activities->perPage(),
                'total'        => $activities->total(),
            ],
        ];
    }

    public function getDetails(string $offerId): ActivityOfferDto
    {
        $activity = Activity::query()
            ->with(['image', 'author'])
            ->published()
            ->find((int) $offerId);

        if (! $activity) {
            throw ActivityException::notFound($offerId);
        }

        return $this->mapper->toOfferDto($activity);
    }

    public function prebook(PrebookActivityDto $dto): ActivityOfferDto
    {
        $activity = Activity::query()
            ->with(['image', 'author'])
            ->published()
            ->find((int) $dto->offerId);

        if (! $activity) {
            throw ActivityException::notFound($dto->offerId);
        }

        if ($activity->max_participants && $dto->participants > $activity->max_participants) {
            throw ActivityException::maxParticipantsExceeded($activity->max_participants);
        }

        return $this->mapper->toOfferDto($activity);
    }

    public function getOrder(string $orderId): ActivityOrderDto
    {
        $booking = Booking::query()
            ->where('code', $orderId)
            ->where('object_model', 'activity')
            ->firstOrFail();

        $passenger = ActivityBookingPassenger::query()
            ->where('booking_id', $booking->id)
            ->first();

        $passengers = $booking->getJsonMeta('activity_passengers');
        $passengers = is_array($passengers) ? array_values(array_filter($passengers, 'is_array')) : [];

        return new ActivityOrderDto(
            orderId:          $booking->code,
            provider:         ActivityProviderEnum::Local,
            activityTitle:    (string) $booking->getMeta('activity_title', ''),
            category:         (string) $booking->getMeta('activity_category', ''),
            city:             (string) $booking->getMeta('activity_city', ''),
            activityDate:     (string) $booking->getMeta('activity_date', ''),
            participants:     (int) $booking->getMeta('activity_participants', 1),
            duration:         (string) $booking->getMeta('activity_duration', ''),
            unitPrice:        (float) $booking->getMeta('activity_unit_price', 0),
            totalPrice:       (float) $booking->total,
            currency:         $booking->currency,
            status:           $booking->status,
            contactEmail:     $booking->email,
            contactPhone:     $booking->phone,
            guestFirstName:   $booking->first_name,
            guestLastName:    $booking->last_name,
            specialRequests:  $booking->customer_notes,
            passengers:       $passengers,
        );
    }

    public function cancelOrder(string $orderId): bool
    {
        $booking = Booking::query()
            ->where('code', $orderId)
            ->where('object_model', 'activity')
            ->firstOrFail();

        $booking->update(['status' => 'cancelled']);

        return true;
    }
}
