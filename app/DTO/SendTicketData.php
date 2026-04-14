<?php

declare(strict_types=1);

namespace App\DTO;

readonly class SendTicketData
{
    public function __construct(
        public string $ticketId,
    ) {}
}
