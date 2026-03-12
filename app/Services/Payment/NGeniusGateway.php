<?php

namespace App\Services\Payment;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NGeniusGateway implements PaymentGatewayInterface
{
    private string $apiKey;
    private string $outletRef;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey    = config('payment.ngenius.api_key', '');
        $this->outletRef = config('payment.ngenius.outlet_ref', '');
        $this->baseUrl   = rtrim(config('payment.ngenius.base_url', 'https://api-gateway.sandbox.ngenius-payments.com'), '/');
    }

    // ── Public API ────────────────────────────────────────────────

    public function initiate(Booking $booking): array
    {
        $token    = $this->requestAccessToken();
        $currency = $this->resolveCurrency($booking);
        $amount   = (int) round($booking->pay_now * 100); // minor units

        $payload = [
            'action' => 'SALE',
            'amount' => [
                'currencyCode' => $currency,
                'value'        => $amount,
            ],
            'merchantOrderReference' => $booking->code,
            'emailAddress'           => $booking->email ?? '',
            'merchantAttributes'     => [
                'redirectUrl' => route('payments.ngenius.return') . '?c=' . $booking->code,
                'cancelUrl'   => route('payments.ngenius.return') . '?c=' . $booking->code . '&cancelled=1',
            ],
        ];

        $response = Http::timeout(30)
            ->withToken($token)
            ->withHeaders([
                'Accept'       => 'application/vnd.ni-payment.v2+json',
                'Content-Type' => 'application/vnd.ni-payment.v2+json',
            ])
            ->post("{$this->baseUrl}/transactions/outlets/{$this->outletRef}/orders", $payload);

        if (! $response->successful()) {
            Log::warning('N-Genius: create order failed', [
                'booking_id' => $booking->id,
                'status'     => $response->status(),
                'body'       => $response->body(),
            ]);
            throw new \RuntimeException('N-Genius order creation failed: ' . $response->body());
        }

        $order      = $response->json();
        $orderRef   = (string) ($order['reference'] ?? '');
        $paymentUrl = (string) (
            $order['_links']['payment']['href']
            ?? $order['_links']['payment-authorization']['href']
            ?? ''
        );

        if (empty($paymentUrl)) {
            throw new \RuntimeException('N-Genius did not return a payment URL.');
        }

        $booking->updateMeta('ngenius_order_ref', $orderRef);

        Payment::create([
            'booking_id'      => $booking->id,
            'payment_gateway' => 'ngenius',
            'status'          => 'draft',
            'amount'          => $booking->pay_now,
            'currency'        => $currency,
            'transaction_id'  => $orderRef,
        ]);

        return ['url' => $paymentUrl];
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

        if (in_array($booking->status, [Booking::COMPLETED, Booking::CONFIRMED], true)) {
            return redirect($booking->getDetailUrl());
        }

        try {
            $order = $this->retrieveOrder((string) $booking->getMeta('ngenius_order_ref', ''));
        } catch (\Throwable $e) {
            Log::warning('N-Genius: handleReturn could not retrieve order', ['error' => $e->getMessage()]);
            return redirect($booking->getDetailUrl())->with('error', 'Could not verify payment. Please contact support.');
        }

        $state = strtoupper((string) ($order['state'] ?? ''));
        $booking->updateMeta('ngenius_order_state', $state);

        if (in_array($state, ['PURCHASED', 'CAPTURED', 'PAID', 'AUTHORISED', 'AUTHORIZED'], true)) {
            $this->markPaid($booking, $order);
            return redirect($booking->getDetailUrl())->with('success', 'Payment confirmed! Your booking is complete.');
        }

        if (in_array($state, ['FAILED', 'DECLINED', 'CANCELLED', 'EXPIRED'], true)) {
            $booking->markAsPaymentFailed();
            return redirect($booking->getDetailUrl())->with('error', 'Payment failed. Please try again.');
        }

        // Still pending — show the booking page; the webhook will finalize it
        return redirect($booking->getDetailUrl())->with('info', 'Payment is being processed. We will notify you when confirmed.');
    }

    public function handleWebhook(Request $request): JsonResponse
    {
        if (! $this->verifyWebhook($request)) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $payload   = $request->json()->all();
        $orderRef  = (string) ($payload['reference'] ?? $payload['order']['reference'] ?? '');
        $bookingCode = (string) ($payload['merchantOrderReference'] ?? $payload['order']['merchantOrderReference'] ?? '');

        $booking = Booking::where('code', $bookingCode)->first();
        if (! $booking) {
            return response()->json(['status' => 'booking_not_found']);
        }

        try {
            $order = $this->retrieveOrder($orderRef ?: (string) $booking->getMeta('ngenius_order_ref', ''));
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }

        $state = strtoupper((string) ($order['state'] ?? ''));

        if (in_array($state, ['PURCHASED', 'CAPTURED', 'PAID', 'AUTHORISED', 'AUTHORIZED'], true)) {
            $this->markPaid($booking, $order);
        } elseif (in_array($state, ['FAILED', 'DECLINED', 'CANCELLED', 'EXPIRED'], true)) {
            $booking->markAsPaymentFailed();
        }

        return response()->json(['status' => 'ok']);
    }

    // ── Private helpers ───────────────────────────────────────────

    private function markPaid(Booking $booking, array $order): void
    {
        if (in_array($booking->status, [Booking::COMPLETED, Booking::CONFIRMED], true)) {
            return;
        }

        Payment::where('booking_id', $booking->id)
            ->where('payment_gateway', 'ngenius')
            ->where('status', 'draft')
            ->update(['status' => 'completed', 'logs' => json_encode($order)]);

        $booking->markAsPaid();
    }

    private function requestAccessToken(): string
    {
        $apiKey = ltrim($this->apiKey, 'Basic ');

        $response = Http::timeout(25)
            ->withHeaders([
                'Authorization' => 'Basic ' . $apiKey,
                'Content-Type'  => 'application/vnd.ni-identity.v1+json',
                'Accept'        => 'application/vnd.ni-identity.v1+json',
            ])
            ->withBody('{}', 'application/vnd.ni-identity.v1+json')
            ->post("{$this->baseUrl}/identity/auth/access-token");

        if (! $response->successful()) {
            throw new \RuntimeException('N-Genius authentication failed.');
        }

        $token = (string) ($response->json('access_token') ?? '');
        if (empty($token)) {
            throw new \RuntimeException('N-Genius access token missing in response.');
        }

        return $token;
    }

    private function retrieveOrder(string $orderRef): array
    {
        $token = $this->requestAccessToken();

        $response = Http::timeout(30)
            ->withToken($token)
            ->withHeaders(['Accept' => 'application/vnd.ni-payment.v2+json'])
            ->get("{$this->baseUrl}/transactions/outlets/{$this->outletRef}/orders/{$orderRef}");

        if (! $response->successful()) {
            throw new \RuntimeException('N-Genius: could not retrieve order ' . $orderRef);
        }

        return $response->json() ?: [];
    }

    private function resolveCurrency(Booking $booking): string
    {
        $forced = strtoupper(trim(config('payment.ngenius.currency', '')));
        return $forced ?: strtoupper($booking->currency ?: 'USD');
    }

    private function verifyWebhook(Request $request): bool
    {
        $secret = config('payment.ngenius.webhook_secret', '');
        if (empty($secret)) {
            return true;
        }

        $signature = (string) $request->header('X-Signature', '');
        if (empty($signature)) {
            return false;
        }

        $computed = base64_encode(hash_hmac('sha256', $request->getContent(), $secret, true));
        return hash_equals($computed, $signature);
    }
}
