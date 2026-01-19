<?php

namespace App\Services\Sms;

interface SmsProviderInterface
{
    /**
     * Send a single SMS message.
     *
     * @param string $phone
     * @param string $message
     * @return bool
     */
    public function send(string $phone, string $message): bool;

    /**
     * Send bulk SMS messages.
     *
     * @param array $phones
     * @param string $message
     * @return array Array of success/failure for each phone.
     */
    public function sendBulk(array $phones, string $message): array;
}
