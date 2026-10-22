<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $locale = $user?->role === 'resident' ? session('locale', 'en') : 'en';

        app()->setLocale(in_array($locale, ['en', 'tl'], true) ? $locale : 'en');

        return $next($request);
    }
}
