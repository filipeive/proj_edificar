<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HttpsmsProvider implements SmsProviderInterface
{
    public function send(string $phone, string $message): bool
    {
        $apiKey = config('services.httpsms.key');
        $from = config('services.httpsms.from');
        $apiUrl = 'https://api.httpsms.com/v1/messages/send';

        if (!$apiKey || !$from) {
            Log::warning('httpsms SMS not sent: missing API key or sender phone (from).');
            return false;
        }

        // Standard phone format for httpsms usually requires a "+" sign prefix if not already present
        $formattedTo = $phone;
        if (!str_starts_with($formattedTo, '+')) {
            $formattedTo = '+' . $formattedTo;
        }

        Log::info('httpsms SMS request', [
            'to' => $formattedTo,
            'from' => $from,
        ]);

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'Content-Type' => 'application/json',
        ])->post($apiUrl, [
            'content' => $message,
            'from' => $from,
            'to' => $formattedTo,
            'encrypted' => false,
        ]);

        if (!$response->successful()) {
            Log::error('httpsms SMS failed.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return false;
        }

        $payload = $response->json();
        Log::info('httpsms SMS response', [
            'status' => $response->status(),
            'body' => $payload ?? $response->body(),
        ]);

        return true;
    }

    public function sendBulk(array $phones, string $message): array
    {
        $results = [];
        foreach ($phones as $phone) {
            $results[$phone] = $this->send($phone, $message);
        }
        return $results;
    }
}
