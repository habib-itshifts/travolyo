<?php

namespace Modules\Hotel\Actions;

use App\Models\Booking;
use App\Models\User;
use App\Services\Payment\PaymentService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Modules\Hotel\DTOs\CheckoutHotelDto;
use Modules\Hotel\DTOs\PrebookHotelDto;
use Modules\Hotel\Enums\HotelProviderEnum;
use Modules\Hotel\Exceptions\HotelException;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Providers\HotelProviderFactory;

class CheckoutHotelAction
{
    public function handle(CheckoutHotelDto $dto): array
    {
        $hotel = Cache::get('hotel_checkout_' . $dto->checkoutToken);

        if (empty($hotel)) {
            throw new \RuntimeException('Hotel session expired. Please search and select your room again.');
        }

        $checkIn  = Carbon::parse($hotel['check_in']);
        $checkOut = Carbon::parse($hotel['check_out']);

        if ($checkOut->lte($checkIn)) {
            throw HotelException::invalidDates();
        }

        $providerEnum = HotelProviderEnum::from($hotel['provider'] ?? 'local');
        $provider     = HotelProviderFactory::make($providerEnum);

        // ── Re-confirm price via prebook (all external providers) ──
        if ($providerEnum !== HotelProviderEnum::Local) {
            $prebookDto = new PrebookHotelDto(
                offerId:  $hotel['offer_id'],
                roomId:   $hotel['room_id'],
                provider: $providerEnum,
                checkIn:  $hotel['check_in'],
                checkOut: $hotel['check_out'],
                adults:   (int) ($hotel['adults'] ?? 1),
                children: (int) ($hotel['children'] ?? 0),
                currency: $hotel['currency'],
            );

            

            $confirmedOffer = $provider->prebook($prebookDto);
            $confirmedRoom  = collect($confirmedOffer->rooms)->first();

            if ($confirmedRoom) {
                $hotel['total_price'] = $confirmedRoom->baseTotalPrice;
                $hotel['unit_price']  = $confirmedRoom->baseCurrentPrice;
                $hotel['currency']    = $confirmedRoom->baseCurrency ?: $hotel['currency'];
            }
        }

        // ── Resolve vendor + commission (local hotels only) ──
        $vendorId       = null;
        $commissionType = null;
        $commissionRate = null;

        if ($providerEnum === HotelProviderEnum::Local && ! empty($hotel['offer_id'])) {
            $hotelModel = Hotel::find((int) $hotel['offer_id']);
            if ($hotelModel?->author_id) {
                $vendorId = $hotelModel->author_id;
                $vendor   = User::find($vendorId);
                if ($vendor) {
                    $commissionType = $vendor->vendor_commission_type;
                    $commissionRate = $vendor->vendor_commission_amount;
                }
            }
        }

        // ── Store guest details for post-payment book() call ──
        $hotel['guest_details'] = [
            'first_name' => $dto->firstName,
            'last_name'  => $dto->lastName,
            'email'      => $dto->email,
            'phone'      => $dto->phone,
            'title'      => 'MR',
            'birth_date' => '1990-01-01',
            'address'    => 'N/A',
            'city'       => 'N/A',
            'country'    => config('hotel.default_nationality', 'AE'),
            'state'      => 'N/A',
            'zip'        => 'N/A',
        ];

        $hotel['special_requests'] = $dto->specialRequests;
        $hotel['extra_services']   = $dto->extraServices;

        // ── Create draft booking (same for ALL providers) ──
        $booking = Booking::create([
            'object_model'    => 'hotel',
            'object_id'       => $providerEnum === HotelProviderEnum::Local ? (int) ($hotel['offer_id'] ?? null) : null,
            'author_id'       => $dto->customerId,
            'customer_id'     => $dto->customerId,
            'vendor_id'       => $vendorId,
            'status'          => 'draft',
            'start_date'      => $checkIn,
            'end_date'        => $checkOut,
            'total_guests'    => ($hotel['adults'] ?? 1) + ($hotel['children'] ?? 0),
            'currency'        => $hotel['currency'],
            'total'           => $hotel['total_price'],
            'pay_now'         => $hotel['total_price'],
            'paid'            => 0,
            'commission_type'   => $commissionType,
            'commission'        => $commissionRate,
            'first_name'      => $dto->firstName,
            'last_name'       => $dto->lastName,
            'email'           => $dto->email,
            'phone'           => $dto->phone,
            'customer_notes'  => $dto->specialRequests,
            'source'          => $providerEnum->value,
            'platform'        => Booking::detectPlatform(),
        ]);

        if ($commissionType && $commissionRate) {
            $booking->applyCommission();
            $booking->save();
        }

        // ── Save checkout data as meta (used by book() after payment) ──
        $booking->addMeta('hotel_details', $hotel);
        $booking->addMeta('payment_gateway', $dto->paymentGateway);

        if ($dto->specialRequests) {
            $booking->addMeta('special_requests', $dto->specialRequests);
        }

        if (! empty($dto->extraServices)) {
            $booking->addMeta('extra_services', $dto->extraServices);
        }

        // ── Initiate payment ──
        $result = PaymentService::gateway($dto->paymentGateway)->initiate($booking);
            
        Cache::forget('hotel_checkout_' . $dto->checkoutToken);

        return ['url' => $result['url']];
    }
}
