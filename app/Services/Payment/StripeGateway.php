<?php

namespace App\Services\Payment;

use App\Enums\BookingObjectModelEnum;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeGateway implements PaymentGatewayInterface
{
    public function __construct()
    {
        \Stripe\Stripe::setApiKey($this->secretKey());
    }

    // ── Public API ────────────────────────────────────────────────

    public function initiate(Booking $booking): array
    {
        $session = \Stripe\Checkout\Session::create([
            'mode'           => 'payment',
            'customer_email' => $booking->email ?: null,
            'success_url'    => route('payments.stripe.return') . '?c=' . $booking->code . '&session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'     => route('payments.stripe.cancel') . '?c=' . $booking->code,
            'line_items'     => [[
                'price_data' => [
                    'currency'     => strtolower($booking->currency ?: 'usd'),
                    'unit_amount'  => (int) round($booking->pay_now * 100),
                    'product_data' => ['name' => $this->bookingTitle($booking)],
                ],
                'quantity' => 1,
            ]],
            'metadata' => [
                'booking_code' => $booking->code,
                'booking_id'   => $booking->id,
            ],
        ]);

        // Store session ID on the booking for webhook reconciliation
        $booking->updateMeta('stripe_session_id', $session->id);

        // Create a draft payment record
        Payment::create([
            'booking_id'       => $booking->id,
            'payment_gateway'  => 'stripe',
            'status'           => 'draft',
            'amount'           => $booking->pay_now,
            'currency'         => $booking->currency,
            'transaction_id'   => $session->id,
        ]);

        return ['url' => $session->url];
    }

    public function handleReturn(Request $request): RedirectResponse
    {
        $booking = Booking::where('code', $request->query('c'))->first();

        if (! $booking) {
            return redirect('/');
        }

        if ($request->boolean('cancelled')) {
            return redirect($booking->getDetailUrl())->with('error', 'Payment was cancelled.');
        }

        // Already finalised (e.g. webhook arrived first)
        if (in_array($booking->status, [Booking::COMPLETED, Booking::CONFIRMED, Booking::CANCELLED], true)) {
            return redirect($booking->getDetailUrl());
        }

        $sessionId = $request->query('session_id');

        try {
            $session = \Stripe\Checkout\Session::retrieve($sessionId);
        } catch (\Throwable $e) {
            Log::warning('Stripe: could not retrieve session on return', ['error' => $e->getMessage()]);
            return redirect($booking->getDetailUrl())->with('error', 'Could not verify payment. Please contact support.');
        }

        if (($session->payment_status ?? '') === 'paid') {
            $this->markPaid($booking, $session->id, json_encode($session->toArray()));
            return redirect($booking->getDetailUrl())->with('success', 'Payment confirmed! Your booking is complete.');
        }

        return redirect($booking->getDetailUrl())->with('error', 'Payment was not completed.');
    }

    public function handleWebhook(Request $request): JsonResponse
    {
        $secret = config('payment.stripe.webhook_secret');

        try {
            $event = $secret
                ? \Stripe\Webhook::constructEvent(
                    $request->getContent(),
                    $request->header('Stripe-Signature'),
                    $secret
                )
                : \Stripe\Event::constructFrom(json_decode($request->getContent(), true));
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $booking = Booking::where('code', $session->metadata->booking_code ?? '')->first();

            if ($booking && ($session->payment_status ?? '') === 'paid') {
                $this->markPaid($booking, $session->id, json_encode($session->toArray()));
            }
        }

        return response()->json(['status' => 'ok']);
    }

    // ── Private helpers ───────────────────────────────────────────

    private function markPaid(Booking $booking, string $sessionId, string $logs): void
    {
        if (in_array($booking->status, [Booking::COMPLETED, Booking::CONFIRMED], true)) {
            return; // idempotent
        }

        Payment::where('transaction_id', $sessionId)
            ->where('booking_id', $booking->id)
            ->update(['status' => 'completed', 'logs' => $logs]);

        $booking->markAsPaid();
    }

    private function bookingTitle(Booking $booking): string
    {
        if ($booking->object_model === BookingObjectModelEnum::Flight->value) {
            $f = $booking->getJsonMeta('flight_details');
            $route = ($f['dep_iata'] ?? '') . ' → ' . ($f['arr_iata'] ?? '');
            return 'Flight Booking — ' . $route;
        }

        return match ($booking->object_model) {
            BookingObjectModelEnum::Hotel->value => 'Hotel Booking',
            BookingObjectModelEnum::Activity->value => 'Activity Booking',
            default                              => 'Travolyo Booking',
        };
    }

    private function secretKey(): string
    {
        return config('payment.stripe.secret_key', '');
    }
}
