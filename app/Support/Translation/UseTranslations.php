<?php

declare(strict_types=1);

namespace App\Support\Translation;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class UseTranslations
{
    public function __construct(public string $key) {}
}
