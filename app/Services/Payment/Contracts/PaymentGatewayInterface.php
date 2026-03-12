<?php

namespace App\Services\Payment\Contracts;

use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    /**
     * Create a payment session / hosted order and return the redirect URL.
     *
     * @return array{url: string}
     */
    public function initiate(Booking $booking): array;

    /**
     * Handle the browser return after the customer pays (or cancels).
     */
    public function handleReturn(Request $request): RedirectResponse;

    /**
     * Handle an incoming webhook from the payment provider.
     */
    public function handleWebhook(Request $request): JsonResponse;
}
