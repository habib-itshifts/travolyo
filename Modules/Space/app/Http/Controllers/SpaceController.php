<?php

namespace Modules\Space\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Space\Services\SpaceService;

class SpaceController extends Controller
{
    public function __construct(private SpaceService $service) {}

    /**
     * Space (Homes & Apts) search/listing page.
     * Actual search is handled by Api\SpaceController@search via AJAX.
     */
    public function index(Request $request): View
    {
        $adults   = max(1, (int) $request->query('adults', 1));
        $children = max(0, (int) $request->query('children', 0));
        $infants  = max(0, (int) $request->query('infants', 0));

        $params = [
            'destination' => (string) $request->query('destination', ''),
            'city'        => (string) $request->query('city', ''),
            'check_in'    => (string) $request->query('check_in', now()->addDays(4)->toDateString()),
            'check_out'   => (string) $request->query('check_out', now()->addDays(8)->toDateString()),
            'adults'      => $adults,
            'children'    => $children,
            'infants'     => $infants,
            'guests'      => $adults + $children + $infants,
            'currency'    => (string) $request->query('currency', 'USD'),
        ];

        return view('space::homes.index', compact('params'));
    }

    /**
     * Space detail page — shows full info + book button.
     */
    public function detail(Request $request): View|RedirectResponse
    {
        
        
        $request->validate([
            'space_id'  => 'required|integer',
            'check_in'  => 'required|date_format:Y-m-d',
            'check_out' => 'required|date_format:Y-m-d|after:check_in',
        ]);

        $space = $this->service->find((int) $request->query('space_id'));

        if (! $space) {
            return redirect()->route('homes.index')->with('error', 'Space not found.');
        }

        $images = $space->orderedImages();

        $params = [
            'space_id'    => $space->id,
            'destination' => (string) $request->query('destination', $space->city ?? $space->name ?? ''),
            'city'        => (string) $request->query('city', $space->city ?? ''),
            'check_in'    => (string) $request->query('check_in'),
            'check_out'   => (string) $request->query('check_out'),
            'adults'      => max(1, (int) $request->query('adults', $request->query('guests', 1))),
            'children'    => max(0, (int) $request->query('children', 0)),
            'infants'     => max(0, (int) $request->query('infants', 0)),
            'currency'    => (string) $request->query('currency', 'USD'),
        ];

        return view('space::homes.detail', compact('space', 'images', 'params'));
    }

    /**
     * Space checkout page — loads book data from cache via token.
     */
    public function checkout(Request $request): View|RedirectResponse
    {
        $token = $request->get('token');
        $sc    = $token ? Cache::get('space_checkout_' . $token) : null;

        if (! $sc) {
            return redirect()->route('homes.index');
        }

        return view('space::homes.checkout', ['sc' => $sc, 'checkout_token' => $token]);
    }

    /**
     * Space booking confirmation page.
     */
    public function confirmation(string $code): View|RedirectResponse
    {
        $booking = Booking::where('code', $code)
            ->where('object_model', 'space')
            ->first();

        if (! $booking) {
            return redirect()->route('homes.index');
        }

        $spaceDetails = $booking->getJsonMeta('space_details') ?? [];
        $gateway      = $booking->getMeta('payment_gateway') ?? '-';

        return view('space::homes.confirmation', compact('booking', 'spaceDetails', 'gateway'));
    }
}
