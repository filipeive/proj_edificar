<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MoceanSmsProvider implements SmsProviderInterface
{
    public function send(string $phone, string $message): bool
    {
        $apiKey = config('services.sms.api_key');
        $apiUrl = config('services.sms.api_url', 'https://rest.moceanapi.com/rest/2/sms');
        $senderId = config('services.sms.sender_id', 'MOCEAN');

        if (!$apiKey) {
            Log::warning('Mocean SMS not sent: missing API key.');
            return false;
        }

        Log::info('Mocean SMS request', [
            'to' => $phone,
            'from' => $senderId,
            'url' => $apiUrl,
        ]);

        $response = Http::asForm()
            ->withToken($apiKey)
            ->post($apiUrl, [
                'mocean-from' => $senderId,
                'mocean-to' => $phone,
                'mocean-text' => $message,
            ]);

        if (!$response->successful()) {
            Log::error('Mocean SMS failed.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return false;
        }

        $payload = $response->json();
        Log::info('Mocean SMS response', [
            'status' => $response->status(),
            'body' => $payload ?? $response->body(),
        ]);
        if (is_array($payload) && isset($payload['messages'][0]['status'])) {
            return (string) $payload['messages'][0]['status'] === '0';
        }

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
