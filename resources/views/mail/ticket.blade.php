@php
    use App\Models\Booking;

    /** @var Booking $booking */
    $screening = $booking->screening;
    $hall = $screening->hall;
    $cinema = $hall->cinema;
    $movie = $screening->movie;
    $bookedSeats = $booking->bookedSeats;
    $total = $bookedSeats->sum('price') / 100;
@endphp
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bilet do kina</title>
</head>
<body style="margin: 0; padding: 24px; background-color: #f4f4f5; color: #18181b; font-family: Arial, Helvetica, sans-serif;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse: collapse;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 680px; border-collapse: collapse; overflow: hidden; background-color: #ffffff; border-radius: 24px;">
                <tr>
                    <td style="padding: 32px; background: linear-gradient(135deg, #111827 0%, #27272a 100%); color: #ffffff;">
                        <p style="margin: 0 0 8px; font-size: 12px; letter-spacing: 0.24em; text-transform: uppercase; color: #d4d4d8;">
                            Bilet do kina
                        </p>
                        <h1 style="margin: 0 0 12px; font-size: 32px; line-height: 1.2;">
                            {{ $movie->title }}
                        </h1>
                        <p style="margin: 0; font-size: 16px; color: #e4e4e7;">
                            Zamówienie nr {{ $booking->booking_number }}
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 32px;">
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse: separate; border-spacing: 0 12px;">
                            <tr>
                                <td style="width: 50%; padding: 16px; background-color: #fafafa; border-radius: 16px; vertical-align: top;">
                                    <p style="margin: 0 0 6px; font-size: 12px; text-transform: uppercase; letter-spacing: 0.08em; color: #71717a;">Data</p>
                                    <p style="margin: 0; font-size: 18px; font-weight: 700;">{{ $screening->starts_at->locale('pl')->translatedFormat('j F Y') }}</p>
                                </td>
                                <td style="width: 50%; padding: 16px; background-color: #fafafa; border-radius: 16px; vertical-align: top;">
                                    <p style="margin: 0 0 6px; font-size: 12px; text-transform: uppercase; letter-spacing: 0.08em; color: #71717a;">Godzina</p>
                                    <p style="margin: 0; font-size: 18px; font-weight: 700;">{{ $screening->starts_at->format('H:i') }} - {{ $screening->ends_at->format('H:i') }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 50%; padding: 16px; background-color: #fafafa; border-radius: 16px; vertical-align: top;">
                                    <p style="margin: 0 0 6px; font-size: 12px; text-transform: uppercase; letter-spacing: 0.08em; color: #71717a;">Kino</p>
                                    <p style="margin: 0; font-size: 18px; font-weight: 700;">{{ $cinema->city }}</p>
                                    <p style="margin: 8px 0 0; font-size: 14px; color: #52525b;">{{ $cinema->street }}</p>
                                </td>
                                <td style="width: 50%; padding: 16px; background-color: #fafafa; border-radius: 16px; vertical-align: top;">
                                    <p style="margin: 0 0 6px; font-size: 12px; text-transform: uppercase; letter-spacing: 0.08em; color: #71717a;">Sala</p>
                                    <p style="margin: 0; font-size: 18px; font-weight: 700;">{{ $hall->label }}</p>
                                    <p style="margin: 8px 0 0; font-size: 14px; color: #52525b;">Adres e-mail: {{ $booking->customer_email }}</p>
                                </td>
                            </tr>
                        </table>

                        <div style="margin-top: 24px; padding: 24px; border: 1px dashed #d4d4d8; border-radius: 20px;">
                            <h2 style="margin: 0 0 16px; font-size: 20px;">Twoje miejsca</h2>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse: collapse;">
                                <thead>
                                <tr>
                                    <th align="left" style="padding: 0 0 12px; font-size: 12px; text-transform: uppercase; letter-spacing: 0.08em; color: #71717a; border-bottom: 1px solid #e4e4e7;">Miejsce</th>
                                    <th align="left" style="padding: 0 0 12px; font-size: 12px; text-transform: uppercase; letter-spacing: 0.08em; color: #71717a; border-bottom: 1px solid #e4e4e7;">Typ</th>
                                    <th align="right" style="padding: 0 0 12px; font-size: 12px; text-transform: uppercase; letter-spacing: 0.08em; color: #71717a; border-bottom: 1px solid #e4e4e7;">Cena</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($bookedSeats as $bookedSeat)
                                    <tr>
                                        <td style="padding: 14px 0; border-bottom: 1px solid #f4f4f5; font-size: 15px; font-weight: 700;">
                                            {{ $bookedSeat->seat->row_label->value }}{{ $bookedSeat->seat->seat_number }}
                                        </td>
                                        <td style="padding: 14px 0; border-bottom: 1px solid #f4f4f5; font-size: 15px; color: #52525b;">
                                            {{ $bookedSeat->seat->seat_type->value }}
                                        </td>
                                        <td align="right" style="padding: 14px 0; border-bottom: 1px solid #f4f4f5; font-size: 15px; font-weight: 700;">
                                            {{ number_format($bookedSeat->price / 100, 2, ',', ' ') }} zł
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-top: 16px; border-collapse: collapse;">
                                <tr>
                                    <td style="font-size: 14px; color: #52525b;">Liczba biletów: {{ $bookedSeats->count() }}</td>
                                    <td align="right" style="font-size: 18px; font-weight: 700;">Razem: {{ number_format($total, 2, ',', ' ') }} zł</td>
                                </tr>
                            </table>
                        </div>

                        <p style="margin: 24px 0 0; font-size: 14px; line-height: 1.6; color: #52525b;">
                            Pokaż ten e-mail przy wejściu na salę. Miłego seansu.
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
