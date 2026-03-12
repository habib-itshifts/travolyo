<?php

namespace App\Http\Controllers;

use App\Services\Payment\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    // ── Stripe ────────────────────────────────────────────────────

    public function stripeReturn(Request $request): RedirectResponse
    {
        return PaymentService::gateway('stripe')->handleReturn($request);
    }

    public function stripeCancel(Request $request): RedirectResponse
    {
        return PaymentService::gateway('stripe')->handleReturn(
            $request->merge(['cancelled' => '1'])
        );
    }

    public function stripeWebhook(Request $request): JsonResponse
    {
        return PaymentService::gateway('stripe')->handleWebhook($request);
    }

    // ── N-Genius ──────────────────────────────────────────────────

    public function ngeniusReturn(Request $request): RedirectResponse
    {
        return PaymentService::gateway('ngenius')->handleReturn($request);
    }

    public function ngeniusWebhook(Request $request): JsonResponse
    {
        return PaymentService::gateway('ngenius')->handleWebhook($request);
    }
}
