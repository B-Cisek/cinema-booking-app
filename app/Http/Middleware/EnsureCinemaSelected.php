<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\Identity\CinemaResolver;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

readonly class EnsureCinemaSelected
{
    public function __construct(private CinemaResolver $cinemaResolver) {}

    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $cinema = $this->cinemaResolver->resolve($request);

        if ($cinema === null) {
            return redirect()->route('home');
        }

        return $next($request);
    }
}
