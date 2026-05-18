<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Commands\ConfirmReservationPayment;
use Illuminate\Container\Attributes\Config;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayuController extends Controller
{
    public function __construct(
        private readonly ConfirmReservationPayment $confirmScreeningReservationPayment,
        #[Config('services.payu.second_key')] private readonly string $secondKey,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $content = $request->getContent();
        $signatureHeader = $request->header('OpenPayu-Signature', '');

        if (! $this->hasValidSignature($content, $signatureHeader)) {
            Log::warning('Invalid PayU notification signature');

            return response()->json(['message' => 'INVALID_SIGNATURE'], 400);
        }

        $this->confirmScreeningReservationPayment->handle($request->array('order'));

        return response()->json(['message' => 'OK']);
    }

    private function hasValidSignature(string $content, string $signatureHeader): bool
    {
        $incomingSignature = $this->signatureValue($signatureHeader);

        if ($incomingSignature === null || $this->secondKey === '') {
            return false;
        }

        return hash_equals(md5($content.$this->secondKey), $incomingSignature);
    }

    private function signatureValue(string $signatureHeader): ?string
    {
        foreach (explode(';', $signatureHeader) as $part) {
            [$key, $value] = explode('=', trim($part));

            if ($key === 'signature' && is_string($value) && $value !== '') {
                return $value;
            }
        }

        return null;
    }
}
