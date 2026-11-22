<?php

namespace App\Http\Middleware;

use App;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLang
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = ['en', 'ar'];
        $currentLocale = ($request->hasSession() ? $request->session()->get('lang') : null)
            ?? $request->cookie('lang');

        $candidates = [
            $request->header('lang'),
            $request->query('lang'),
            $request->header('X-Language'),
            $request->header('Accept-Language'),
            $currentLocale,
            config('app.locale'),
        ];

        $resolvedLocale = null;
        foreach ($candidates as $candidate) {
            if (!is_string($candidate) || trim($candidate) === '') {
                continue;
            }
            $code = strtolower(substr(trim($candidate), 0, 2));
            if (in_array($code, $supportedLocales, true)) {
                $resolvedLocale = $code;
                break;
            }
        }

        if ($resolvedLocale !== null) {
            App::setLocale($resolvedLocale);
            if ($request->hasSession() && $request->session()->get('lang') !== $resolvedLocale) {
                $request->session()->put('lang', $resolvedLocale);
            }
            if ($request->cookie('lang') !== $resolvedLocale) {
                cookie()->queue('lang', $resolvedLocale, 60 * 24 * 365);
            }
        }

        return $next($request);
    }
}
