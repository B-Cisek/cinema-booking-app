<?php

declare(strict_types=1);

namespace App\Enums;

enum ScreeningStatus: string
{
    case SCHEDULED = 'scheduled';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
}
