<?php

declare(strict_types=1);

namespace App\ViewData;

use App\Enums\BookingStatus;
use App\Models\BookedSeat;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;

readonly class PurchaseHistoryPageData
{
    /**
     * @return array{
     *     bookings: array{
     *         data: list<array{
     *             id: string,
     *             number: string,
     *             purchased_at: string,
     *             status: string,
     *             payment_method: string,
     *             total: int,
     *             screening: array{
     *                 id: string,
     *                 date: string,
     *                 starts_at: string,
     *                 ends_at: string,
     *                 movie: array{
     *                     title: string,
     *                     poster_url: string
     *                 },
     *                 hall: array{
     *                     label: string,
     *                     cinema: array{
     *                         city: string,
     *                         street: string
     *                     }
     *                 }
     *             },
     *             seats: list<array{
     *                 id: string,
     *                 label: string,
     *                 price: int
     *             }>
     *         }>,
     *         pagination: array{
     *             current_page: int,
     *             last_page: int,
     *             per_page: int,
     *             total: int,
     *             from: int|null,
     *             to: int|null,
     *             links: list<array{
     *                 page: int,
     *                 label: string,
     *                 url: string,
     *                 active: bool
     *             }>
     *         }
     *     }
     * }
     */
    public function build(User $user, int $perPage = 5): array
    {
        /** @var LengthAwarePaginator<int, Booking> $bookings */
        $bookings = $user->bookings()
            ->where('status', BookingStatus::CONFIRMED)
            ->with([
                'screening.movie',
                'screening.hall.cinema',
                'bookedSeats.seat',
            ])
            ->withSum('bookedSeats as total_price', 'price')
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();

        return [
            'bookings' => [
                'data' => $bookings
                    ->getCollection()
                    ->map(fn (Booking $booking): array => $this->mapBooking($booking))
                    ->values()
                    ->all(),
                'pagination' => [
                    'current_page' => $bookings->currentPage(),
                    'last_page' => $bookings->lastPage(),
                    'per_page' => $bookings->perPage(),
                    'total' => $bookings->total(),
                    'from' => $bookings->firstItem(),
                    'to' => $bookings->lastItem(),
                    'links' => $this->buildPaginationLinks($bookings),
                ],
            ],
        ];
    }

    /**
     * @return array{
     *     id: string,
     *     number: string,
     *     purchased_at: string,
     *     status: string,
     *     payment_method: string,
     *     total: int,
     *     screening: array{
     *         id: string,
     *         date: string,
     *         starts_at: string,
     *         ends_at: string,
     *         movie: array{
     *             title: string,
     *             poster_url: string
     *         },
     *         hall: array{
     *             label: string,
     *             cinema: array{
     *                 city: string,
     *                 street: string
     *             }
     *         }
     *     },
     *     seats: list<array{
     *         id: string,
     *         label: string,
     *         price: int
     *     }>
     * }
     */
    private function mapBooking(Booking $booking): array
    {
        return [
            'id' => $booking->getKey(),
            'number' => $booking->booking_number,
            'purchased_at' => $booking->created_at
                ->locale(App::currentLocale())
                ->translatedFormat('j F Y, H:i'),
            'status' => $booking->status->label(),
            'payment_method' => $booking->payment_method->label(),
            'total' => (int) ($booking->total_price ?? $booking->bookedSeats->sum('price')),
            'screening' => [
                'id' => $booking->screening->getKey(),
                'date' => $booking->screening->starts_at
                    ->locale(App::currentLocale())
                    ->translatedFormat('j F Y'),
                'starts_at' => $booking->screening->starts_at->format('H:i'),
                'ends_at' => $booking->screening->ends_at->format('H:i'),
                'movie' => [
                    'title' => $booking->screening->movie->title,
                    'poster_url' => $booking->screening->movie->poster_url,
                ],
                'hall' => [
                    'label' => $booking->screening->hall->label,
                    'cinema' => [
                        'city' => $booking->screening->hall->cinema->city,
                        'street' => $booking->screening->hall->cinema->street,
                    ],
                ],
            ],
            'seats' => $booking->bookedSeats
                ->map(fn (BookedSeat $bookedSeat): array => [
                    'id' => $bookedSeat->getKey(),
                    'label' => sprintf(
                        '%s%s',
                        $bookedSeat->seat->row_label->value,
                        $bookedSeat->seat->seat_number,
                    ),
                    'price' => $bookedSeat->price,
                ])
                ->values()
                ->all(),
        ];
    }

    /**
     * @param  LengthAwarePaginator<int, Booking>  $bookings
     * @return list<array{
     *     page: int,
     *     label: string,
     *     url: string,
     *     active: bool
     * }>
     */
    private function buildPaginationLinks(LengthAwarePaginator $bookings): array
    {
        $currentPage = $bookings->currentPage();
        $lastPage = $bookings->lastPage();
        $startPage = max(1, $currentPage - 1);
        $endPage = min($lastPage, $currentPage + 1);

        return collect(range($startPage, $endPage))
            ->map(fn (int $page): array => [
                'page' => $page,
                'label' => (string) $page,
                'url' => $bookings->url($page),
                'active' => $page === $currentPage,
            ])
            ->all();
    }
}
