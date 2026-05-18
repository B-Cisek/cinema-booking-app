<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use App\Providers\TelescopeServiceProvider;
use Resend\Laravel\ResendServiceProvider;

return [
    AppServiceProvider::class,
    TelescopeServiceProvider::class,
    ResendServiceProvider::class,
];
