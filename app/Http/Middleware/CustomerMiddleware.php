<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->hasRole('customer')) {
            abort(403, 'Unauthorized. Customer access required.');
        }

        return $next($request);
    }
}
