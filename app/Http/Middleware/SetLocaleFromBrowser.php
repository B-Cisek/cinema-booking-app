<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromBrowser
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $lang = $request->getPreferredLanguage(['pl', 'en']) ?? Config::get('app.locale', 'pl');

        App::setLocale($lang);

        return $next($request);
    }
}
