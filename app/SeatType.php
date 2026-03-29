<?php

namespace App;

enum SeatType: string
{
    case STANDARD = 'standard';
    case VIP = 'vip';
    case WHEELCHAIR = 'wheelchair';
    case COUPLE = 'couple';
}
