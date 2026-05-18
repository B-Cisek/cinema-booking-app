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
<body bgcolor="#f4f4f5" text="#18181b">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" bgcolor="#f4f4f5" style="border-collapse: collapse;">
    <tr>
        <td align="center" style="padding-top: 24px; padding-right: 24px; padding-bottom: 24px; padding-left: 24px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 680px; border-collapse: collapse; background-color: #ffffff; font-family: Arial, Helvetica, sans-serif;">
                <tr>
                    <td bgcolor="#111827" style="padding-top: 32px; padding-right: 32px; padding-bottom: 32px; padding-left: 32px;background-color: #111827; color: #ffffff;">
                        <div style="padding-top: 0; padding-right: 0; padding-bottom: 8px; padding-left: 0;font-size: 12px; letter-spacing: 0.24em; text-transform: uppercase; color: #d4d4d8;">
                            Bilet do kina
                        </div>
                        <div style="padding-top: 0; padding-right: 0; padding-bottom: 12px; padding-left: 0;font-size: 32px; font-weight: bold; line-height: 1.2;">
                            {{ $movie->title }}
                        </div>
                        <div style="font-size: 16px; color: #e4e4e7;">
                            Zamówienie nr {{ $booking->booking_number }}
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="padding-top: 32px; padding-right: 32px; padding-bottom: 32px; padding-left: 32px;">
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse: separate; border-spacing: 0 12px;">
                            <tr>
                                <td bgcolor="#fafafa" style="width: 48%; padding-top: 16px; padding-right: 16px; padding-bottom: 16px; padding-left: 16px;background-color: #fafafa; vertical-align: top;">
                                    <div style="padding-top: 0; padding-right: 0; padding-bottom: 6px; padding-left: 0;font-size: 12px; text-transform: uppercase; letter-spacing: 0.08em; color: #71717a;">Data</div>
                                    <div style="font-size: 18px; font-weight: bold;">{{ $screening->starts_at->locale('pl')->translatedFormat('j F Y') }}</div>
                                </td>
                                <td style="width: 12px; font-size: 0; line-height: 0;">&nbsp;</td>
                                <td bgcolor="#fafafa" style="width: 48%; padding-top: 16px; padding-right: 16px; padding-bottom: 16px; padding-left: 16px;background-color: #fafafa; vertical-align: top;">
                                    <div style="padding-top: 0; padding-right: 0; padding-bottom: 6px; padding-left: 0;font-size: 12px; text-transform: uppercase; letter-spacing: 0.08em; color: #71717a;">Godzina</div>
                                    <div style="font-size: 18px; font-weight: bold;">{{ $screening->starts_at->format('H:i') }} - {{ $screening->ends_at->format('H:i') }}</div>
                                </td>
                            </tr>
                            <tr>
                                <td bgcolor="#fafafa" style="width: 48%; padding-top: 16px; padding-right: 16px; padding-bottom: 16px; padding-left: 16px;background-color: #fafafa; vertical-align: top;">
                                    <div style="padding-top: 0; padding-right: 0; padding-bottom: 6px; padding-left: 0;font-size: 12px; text-transform: uppercase; letter-spacing: 0.08em; color: #71717a;">Kino</div>
                                    <div style="font-size: 18px; font-weight: bold;">{{ $cinema->city }}</div>
                                    <div style="padding-top: 8px; padding-right: 0; padding-bottom: 0; padding-left: 0;font-size: 14px; color: #52525b;">{{ $cinema->street }}</div>
                                </td>
                                <td style="width: 12px; font-size: 0; line-height: 0;">&nbsp;</td>
                                <td bgcolor="#fafafa" style="width: 48%; padding-top: 16px; padding-right: 16px; padding-bottom: 16px; padding-left: 16px;background-color: #fafafa; vertical-align: top;">
                                    <div style="padding-top: 0; padding-right: 0; padding-bottom: 6px; padding-left: 0;font-size: 12px; text-transform: uppercase; letter-spacing: 0.08em; color: #71717a;">Sala</div>
                                    <div style="font-size: 18px; font-weight: bold;">{{ $hall->label }}</div>
                                    <div style="padding-top: 8px; padding-right: 0; padding-bottom: 0; padding-left: 0;font-size: 14px; color: #52525b;">Adres e-mail: {{ $booking->customer_email }}</div>
                                </td>
                            </tr>
                        </table>

                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse: collapse;">
                            <tr>
                                <td style="height: 24px; line-height: 24px; font-size: 24px;">&nbsp;</td>
                            </tr>
                        </table>

                        <div style="padding-top: 24px; padding-right: 24px; padding-bottom: 24px; padding-left: 24px;border: 1px dashed #d4d4d8;">
                            <div style="padding-top: 0; padding-right: 0; padding-bottom: 16px; padding-left: 0;font-size: 20px; font-weight: bold;">Twoje miejsca</div>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse: collapse;">
                                <thead>
                                <tr>
                                    <th align="left" style="padding-top: 0; padding-right: 0; padding-bottom: 12px; padding-left: 0;font-size: 12px; text-transform: uppercase; letter-spacing: 0.08em; color: #71717a; border-bottom: 1px solid #e4e4e7;">Miejsce</th>
                                    <th align="left" style="padding-top: 0; padding-right: 0; padding-bottom: 12px; padding-left: 0;font-size: 12px; text-transform: uppercase; letter-spacing: 0.08em; color: #71717a; border-bottom: 1px solid #e4e4e7;">Typ</th>
                                    <th align="right" style="padding-top: 0; padding-right: 0; padding-bottom: 12px; padding-left: 0;font-size: 12px; text-transform: uppercase; letter-spacing: 0.08em; color: #71717a; border-bottom: 1px solid #e4e4e7;">Cena</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($bookedSeats as $bookedSeat)
                                    <tr>
                                        <td style="padding-top: 14px; padding-right: 0; padding-bottom: 14px; padding-left: 0; border-bottom: 1px solid #f4f4f5; font-size: 15px; font-weight: bold;">
                                            {{ $bookedSeat->seat->row_label->value }}{{ $bookedSeat->seat->seat_number }}
                                        </td>
                                        <td style="padding-top: 14px; padding-right: 0; padding-bottom: 14px; padding-left: 0; border-bottom: 1px solid #f4f4f5; font-size: 15px; color: #52525b;">
                                            {{ $bookedSeat->seat->seat_type->value }}
                                        </td>
                                        <td align="right" style="padding-top: 14px; padding-right: 0; padding-bottom: 14px; padding-left: 0;border-bottom: 1px solid #f4f4f5; font-size: 15px; font-weight: bold;">
                                            {{ number_format($bookedSeat->price / 100, 2, ',', ' ') }} zł
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse: collapse;">
                                <tr>
                                    <td colspan="2" style="height: 16px; line-height: 16px; font-size: 16px;">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td style="font-size: 14px; color: #52525b;">Liczba biletów: {{ $bookedSeats->count() }}</td>
                                    <td align="right" style="font-size: 18px; font-weight: bold;">Razem: {{ number_format($total, 2, ',', ' ') }} zł</td>
                                </tr>
                            </table>
                        </div>

                        <div style="padding-top: 24px; padding-right: 0; padding-bottom: 0; padding-left: 0;font-size: 14px; line-height: 1.6; color: #52525b;">
                            Pokaż ten e-mail przy wejściu na salę. Miłego seansu.
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
