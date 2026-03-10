<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VendorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->hasRole('vendor')) {
            abort(403, 'Unauthorized. Vendor access required.');
        }

        return $next($request);
    }
}
