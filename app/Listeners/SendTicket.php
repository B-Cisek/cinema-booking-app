<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\BookingConfirmed;
use App\Mail\SendTicket as SendTicketMail;
use Illuminate\Support\Facades\Mail;

class SendTicket
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(BookingConfirmed $event): void
    {
        $booking = $event->booking;

        $booking->loadMissing([
            'screening.movie',
            'screening.hall.cinema',
            'bookedSeats.seat',
        ]);

        Mail::to($booking->customer_email)->queue(new SendTicketMail($booking));
    }
}
