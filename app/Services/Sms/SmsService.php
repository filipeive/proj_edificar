<?php

namespace App\Services\Sms;

use Exception;

class SmsService
{
    protected $provider;

    public function __construct()
    {
        $this->provider = $this->resolveProvider();
    }

    protected function resolveProvider(): SmsProviderInterface
    {
        $driver = config('services.sms.driver', 'log');

        switch ($driver) {
            case 'log':
                return new LogSmsProvider();
            case 'mocean':
                return new MoceanSmsProvider();
            case 'httpsms':
                return new HttpsmsProvider();
            default:
                return new LogSmsProvider();
        }
    }

    /**
     * Send a message to a single recipient.
     */
    public function send(string $phone, string $message): bool
    {
        try {
            return $this->provider->send($this->formatPhone($phone), $message);
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    /**
     * Send a message to multiple recipients.
     */
    public function sendBulk(array $phones, string $message): array
    {
        try {
            $formattedPhones = array_map([$this, 'formatPhone'], $phones);
            return $this->provider->sendBulk($formattedPhones, $message);
        } catch (Exception $e) {
            report($e);
            return [];
        }
    }

    /**
     * Basic phone formatting (ensure only digits).
     */
    protected function formatPhone(string $phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', $phone);

        if ($digits !== '' && strpos($digits, '258') !== 0) {
            $digits = '258' . $digits;
        }

        return $digits;
    }
}
