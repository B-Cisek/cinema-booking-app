<?php

declare(strict_types=1);

namespace App\Support\Payment;

use Illuminate\Container\Attributes\Config;
use Illuminate\Support\Facades\Http;

readonly class PayuPaymentGateway implements PaymentGateway
{
    public function __construct(
        #[Config('services.payu.base_url')] private string $url,
        #[Config('services.payu.client_id')] private string $clientId,
        #[Config('services.payu.client_secret')] private string $clientSecret,
        #[Config('services.payu.pos_id')] private string $posId,
        #[Config('services.payu.notify_url')] private string $notifyUrl,
    ) {}

    public function start(Payment $payment): PaymentRedirect
    {
        $token = $this->authenticationRequest();
        $totalAmount = (string) $payment->booking->bookedSeats()->sum('price');
        $quantity = $payment->booking->bookedSeats()->count();

        $orderResponse = Http::withToken($token)
            ->withoutRedirecting()
            ->acceptJson()
            ->asJson()
            ->post(sprintf('%s/api/v2_1/orders', $this->url), [
                'continueUrl' => route('screenings.reservation-success', [
                    'screening' => $payment->booking->screening_id,
                    'booking' => $payment->booking,
                ]),
                'notifyUrl' => $this->notifyUrl,
                'customerIp' => $payment->customerIp,
                'merchantPosId' => $this->posId,
                'description' => sprintf('Rezerwacja %s', $payment->booking->booking_number),
                'currencyCode' => 'PLN',
                'totalAmount' => $totalAmount,
                'extOrderId' => $payment->booking->id,
                'products' => [
                    [
                        'name' => 'Bilety kinowe',
                        'unitPrice' => $totalAmount,
                        'quantity' => (string) $quantity,
                    ],
                ],
            ])
            ->throw();

        return new PaymentRedirect($orderResponse->json('redirectUri') ?? $orderResponse->header('Location'));
    }

    private function authenticationRequest(): string
    {
        $tokenResponse = Http::asForm()
            ->post(sprintf('%s/pl/standard/user/oauth/authorize', $this->url), [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ])
            ->throw();

        return $tokenResponse->json('access_token');
    }
}
