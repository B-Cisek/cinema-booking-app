<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Ramsey\Uuid\Uuid;

readonly class GuestTokenHandler
{
    private const string COOKIE_NAME = 'guest-token';

    private const string REQUEST_ATTRIBUTE_NAME = 'guest-token';

    private const int COOKIE_LIFETIME_IN_MINUTES = 60 * 24 * 30;

    public function resolve(Request $request): string
    {
        return $this->getToken($request) ?: $this->setup($request);
    }

    public function setup(Request $request): string
    {
        $token = $this->getToken($request);

        if (! $token) {
            $token = Uuid::uuid7()->toString();
            $request->attributes->set(self::REQUEST_ATTRIBUTE_NAME, $token);

            Cookie::queue(
                self::COOKIE_NAME,
                $token,
                self::COOKIE_LIFETIME_IN_MINUTES,
            );

            return $token;
        }

        return $token;
    }

    private function getToken(Request $request): ?string
    {
        $token = $request->attributes->get(self::REQUEST_ATTRIBUTE_NAME);

        if (is_string($token) && Uuid::isValid($token)) {
            return $token;
        }

        $token = $request->cookie(self::COOKIE_NAME);

        if (is_string($token) && Uuid::isValid($token)) {
            $request->attributes->set(self::REQUEST_ATTRIBUTE_NAME, $token);

            return $token;
        }

        return null;
    }
}
