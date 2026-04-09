<?php

namespace Modules\Hotel\Actions;

use App\Models\Booking;
use App\Models\User;
use App\Services\Payment\PaymentService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Modules\Hotel\DTOs\CheckoutHotelDto;
use Modules\Hotel\Enums\BookingRoomStatusEnum;
use Modules\Hotel\Exceptions\HotelException;
use Modules\Hotel\Models\BookingRoom;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelRoom;
use Modules\Hotel\Providers\Hyperguest\HyperguestHotelProvider;
use Modules\Hotel\Providers\TravolyoB2B\TravolyoB2BHotelProvider;

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

        $nights   = $checkIn->diffInDays($checkOut);
        $provider = $hotel['provider'] ?? 'local';

        // Resolve vendor + commission from the hotel's owner (local hotels only)
        $vendorId         = null;
        $commissionType   = null;
        $commissionRate   = null;

        if ($provider === 'local' && ! empty($hotel['offer_id'])) {
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

        $travolyoB2BBooking = null;

        // ── Hyperguest: pre-book to confirm price, actual booking happens after payment ──
        if ($provider === 'hyperguest') {
            $prebookDto = new \Modules\Hotel\DTOs\PrebookHotelDto(
                offerId:  $hotel['offer_id'],
                roomId:   $hotel['room_id'],
                provider: \Modules\Hotel\Enums\HotelProviderEnum::Hyperguest,
                checkIn:  $hotel['check_in'],
                checkOut: $hotel['check_out'],
                adults:   (int) ($hotel['adults'] ?? 1),
                children: (int) ($hotel['children'] ?? 0),
                currency: $hotel['currency'],
            );

            (new HyperguestHotelProvider())->prebook($prebookDto);

            // Store guest details in hotel meta — used by Booking::markAsPaid() to call booking/create
            $hotel['guest_details'] = [
                'first_name' => $dto->firstName,
                'last_name'  => $dto->lastName,
                'email'      => $dto->email,
                'phone'      => $dto->phone,
                'title'      => 'MR',
                'birth_date' => '1990-01-01',
                'address'    => 'N/A',
                'city'       => 'N/A',
                'country'    => 'AE',
                'state'      => 'N/A',
                'zip'        => 'N/A',
            ];
        }

        if ($provider === 'travolyo_b2b') {
            $travolyoB2BBooking = (new TravolyoB2BHotelProvider())->book(
                checkoutData: array_merge($hotel, ['special_requests' => $dto->specialRequests]),
                guest: [
                    'first_name'  => $dto->firstName,
                    'last_name'   => $dto->lastName,
                    'email'       => $dto->email,
                    'phone'       => $dto->phone,
                    'nationality' => 'AE',
                ],
            );

            if (! empty($travolyoB2BBooking['confirmed_total_price'])) {
                $hotel['total_price'] = (float) $travolyoB2BBooking['confirmed_total_price'];
            }

            $hotel['supplier_status'] = $travolyoB2BBooking['supplier_status'] ?? ($hotel['supplier_status'] ?? '');
            $hotel['supplier_reference'] = $travolyoB2BBooking['supplier_reference'] ?? ($hotel['supplier_reference'] ?? '');
            $hotel['supplier_booking_code'] = $travolyoB2BBooking['supplier_booking_code'] ?? ($hotel['supplier_booking_code'] ?? '');
        }

        $booking = Booking::create([
            'object_model'    => 'hotel',
            'object_id'       => $provider === 'local' ? (int) ($hotel['offer_id'] ?? null) : null,
            'author_id'       => $dto->customerId,   // self-service: customer is the author
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
            'source'          => $provider,
            'platform'        => Booking::detectPlatform(),
        ]);

        // Calculate commission_amount and vendor_amount now that total is set
        if ($commissionType && $commissionRate) {
            $booking->applyCommission();
            $booking->save();
        }

        // Store hotel details in booking meta for confirmation page
        $booking->addMeta('hotel_details', $hotel);

        // Hyperguest: booking meta is saved in Booking::markAsPaid() after payment succeeds

        if ($travolyoB2BBooking !== null) {
            $booking->addMeta('travolyo_b2b_booking', $travolyoB2BBooking);
            $booking->addMeta('travolyo_b2b_supplier_reference', $travolyoB2BBooking['supplier_reference'] ?? null);
            $booking->addMeta('travolyo_b2b_supplier_booking_code', $travolyoB2BBooking['supplier_booking_code'] ?? null);
            $booking->addMeta('travolyo_b2b_status', $travolyoB2BBooking['supplier_status'] ?? 'unknown');
        }

        $booking->addMeta('payment_gateway', $dto->paymentGateway);

        if ($dto->specialRequests) {
            $booking->addMeta('special_requests', $dto->specialRequests);
        }

        if (! empty($dto->extraServices)) {
            $booking->addMeta('extra_services', $dto->extraServices);
        }

        // Create BookingRoom record only for local hotel rooms (B2B rooms have no local DB record)
        if ($provider === 'local') {
            $room = HotelRoom::find((int) $hotel['room_id']);

            if ($room) {
                BookingRoom::create([
                    'booking_id'       => $booking->id,
                    'hotel_room_id'    => $room->id,
                    'hotel_deal_id'    => $hotel['deal_id'] ?? null, // links to the deal that set the price
                    'check_in'         => $checkIn->toDateString(),
                    'check_out'        => $checkOut->toDateString(),
                    'nights'           => $nights,
                    'adults'           => $hotel['adults'],
                    'children'         => $hotel['children'],
                    'unit_price'       => $hotel['unit_price'],
                    'total_price'      => $hotel['total_price'],
                    'extra_services'   => $dto->extraServices ?: null,
                    'special_requests' => $dto->specialRequests,
                    'status'           => BookingRoomStatusEnum::Pending->value,
                ]);
            }
        }

        $result = PaymentService::gateway($dto->paymentGateway)->initiate($booking);

        Cache::forget('hotel_checkout_' . $dto->checkoutToken);

        return ['url' => $result['url']];
    }
}
