<?php

declare(strict_types=1);

namespace App\Support\Translation;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Str;
use ReflectionException;
use ReflectionMethod;

readonly class TranslationPropResolver
{
    public function __construct(
        private Translator $translator,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function resolve(Request $request): array
    {
        $route = $request->route();

        if (! $route instanceof Route) {
            return [];
        }

        $action = $route->getAction('uses');

        if (! is_string($action)) {
            return [];
        }

        [$controllerClass, $actionMethod] = Str::parseCallback($action);

        if (! is_string($controllerClass) || ! is_string($actionMethod)) {
            return [];
        }

        try {
            $reflectionMethod = new ReflectionMethod($controllerClass, $actionMethod);
        } catch (ReflectionException) {
            return [];
        }

        foreach ($reflectionMethod->getAttributes(UseTranslations::class) as $attribute) {
            /** @var UseTranslations $useTranslations */
            $useTranslations = $attribute->newInstance();

            $translations = $this->translator->get($useTranslations->key);

            if (is_array($translations)) {
                return $translations;
            }

            return [];
        }

        return [];
    }
}
