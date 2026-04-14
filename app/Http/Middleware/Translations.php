<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\TranslationPropResolver;
use Illuminate\Http\Request;
use Inertia\Middleware;

class Translations extends Middleware
{
    public function __construct(
        private readonly TranslationPropResolver $translationPropResolver,
    ) {}

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'lang' => $this->translationPropResolver->resolve($request),
        ];
    }
}
