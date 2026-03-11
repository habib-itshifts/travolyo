<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale', config('language.default', config('app.locale', 'en')));

        if (array_key_exists($locale, config('language.supported', []))) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
