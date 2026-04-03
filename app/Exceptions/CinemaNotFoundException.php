<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class CinemaNotFoundException extends Exception
{
    protected $message = 'Cinema not found.';
}
