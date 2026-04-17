<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromBrowser
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $lang = Config::get('app.locale', 'pl');

        if ($request->hasHeader('Accept-Language')) {
            $preferredLanguage = $request->getPreferredLanguage(['pl', 'en']);

            if (
                is_string($preferredLanguage)
                && Lang::has('global.button.cinema_picker', $preferredLanguage, false)
            ) {
                $lang = $preferredLanguage;
            }
        }

        App::setLocale($lang);

        return $next($request);
    }
}
