<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $cookieLocale = $request->cookie('locale') ?: ($request->cookies->get('locale') ?: ($_COOKIE['locale'] ?? null));
        $sessionLocale = $request->hasSession() ? $request->session()->get('locale') : null;

        // If client-side cookie was updated, prioritize it so switches are instant
        if ($cookieLocale && in_array($cookieLocale, ['id', 'en'], true)) {
            $locale = $cookieLocale;
        } elseif ($sessionLocale && in_array($sessionLocale, ['id', 'en'], true)) {
            $locale = $sessionLocale;
        } else {
            $locale = config('app.locale', 'id');
        }

        if (!in_array($locale, ['id', 'en'], true)) {
            $locale = 'id';
        }

        App::setLocale($locale);
        \Carbon\Carbon::setLocale($locale);

        if ($request->hasSession() && $request->session()->get('locale') !== $locale) {
            $request->session()->put('locale', $locale);
        }

        $response = $next($request);

        $finalLocale = App::getLocale();
        if (!in_array($finalLocale, ['id', 'en'], true)) {
            $finalLocale = $locale;
        }

        if ($response instanceof Response) {
            $hasLocaleCookie = false;
            foreach ($response->headers->getCookies() as $c) {
                if ($c->getName() === 'locale') {
                    $hasLocaleCookie = true;
                    break;
                }
            }

            if (!$hasLocaleCookie && $request->cookie('locale') !== $finalLocale) {
                $response->headers->setCookie(
                    cookie()->forever('locale', $finalLocale, '/', null, false, false)
                );
            }
        }

        return $response;
    }
}
