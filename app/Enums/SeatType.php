<?php

declare(strict_types=1);

namespace App\Enums;

enum SeatType: string
{
    case STANDARD = 'standard';
    case VIP = 'vip';
    case WHEELCHAIR = 'wheelchair';
    case COUPLE = 'couple';
}
