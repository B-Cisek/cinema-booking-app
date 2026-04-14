<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\TranslationPropResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Inertia\Middleware;

class Translations extends Middleware
{
    public function __construct(
        private readonly TranslationPropResolver $translationPropResolver,
    ) {}

    public function shareOnce(Request $request): array
    {
        return [
            'globalLang' => fn () => Lang::get('global'),
            'lang' => fn () => $this->translationPropResolver->resolve($request),
        ];
    }
}
