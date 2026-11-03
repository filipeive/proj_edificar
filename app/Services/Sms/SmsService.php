<?php

namespace App\Services\Sms;

use App\Models\SmsMessage;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $provider;
    protected string $driver;

    public function __construct()
    {
        $this->driver = config('services.sms.driver', 'log');
        $this->provider = $this->resolveProvider();
    }

    protected function resolveProvider(): SmsProviderInterface
    {
        switch ($this->driver) {
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

    public function getDriver(): string
    {
        return $this->driver;
    }

    /**
     * Send a message to a single recipient with automatic database tracking.
     *
     * @param string $phone
     * @param string $message
     * @param array $meta [
     *     'recipient_name' => ?string,
     *     'recipient_type' => ?string,
     *     'recipient_id'   => ?int,
     *     'context'        => ?string,
     *     'reference_type' => ?string,
     *     'reference_id'   => ?int,
     *     'user_id'        => ?int,
     *     'congregation_id'=> ?int,
     * ]
     * @return bool
     */
    public function send(string $phone, string $message, array $meta = []): bool
    {
        $formattedPhone = $this->formatPhone($phone);
        if (empty($formattedPhone)) {
            Log::warning('SmsService: número de telefone vazio ou inválido', ['original' => $phone]);
            return false;
        }

        $charCount = mb_strlen($message);
        $partsCount = $charCount <= 160 ? 1 : (int) ceil($charCount / 153);

        $smsRecord = null;
        try {
            $smsRecord = SmsMessage::create([
                'congregation_id' => $meta['congregation_id'] ?? (app()->bound(\App\Services\CongregationContext::class) ? app(\App\Services\CongregationContext::class)->getActiveCongregationId() : null),
                'user_id'         => $meta['user_id'] ?? Auth::id(),
                'phone'           => $formattedPhone,
                'recipient_name'  => $meta['recipient_name'] ?? null,
                'recipient_type'  => $meta['recipient_type'] ?? null,
                'recipient_id'    => $meta['recipient_id'] ?? null,
                'message'         => $message,
                'characters_count'=> $charCount,
                'parts_count'     => $partsCount,
                'driver'          => $this->driver,
                'status'          => 'pending',
                'context'         => $meta['context'] ?? 'general',
                'reference_type'  => $meta['reference_type'] ?? null,
                'reference_id'    => $meta['reference_id'] ?? null,
            ]);
        } catch (Exception $e) {
            Log::error('SmsService: Falha ao criar registro sms_messages', ['error' => $e->getMessage()]);
        }

        try {
            $success = $this->provider->send($formattedPhone, $message);

            if ($smsRecord) {
                if ($success) {
                    $smsRecord->update([
                        'status'  => 'sent',
                        'sent_at' => now(),
                    ]);
                } else {
                    $smsRecord->update([
                        'status'        => 'failed',
                        'error_message' => 'Provedor ' . $this->driver . ' retornou erro ao enviar o SMS.',
                    ]);
                }
            }

            return $success;
        } catch (Exception $e) {
            report($e);
            if ($smsRecord) {
                $smsRecord->update([
                    'status'        => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            }
            return false;
        }
    }

    /**
     * Send a message to multiple recipients.
     *
     * @param array $recipients Array of phones (strings) or array of associative arrays
     * @param string $message
     * @param array $meta
     * @return array ['total' => int, 'sent' => int, 'failed' => int]
     */
    public function sendBulk(array $recipients, string $message, array $meta = []): array
    {
        $total = 0;
        $sent = 0;
        $failed = 0;

        foreach ($recipients as $recipient) {
            $total++;

            if (is_array($recipient)) {
                $phone = $recipient['phone'] ?? null;
                $recipientMeta = array_merge($meta, [
                    'recipient_name' => $recipient['name'] ?? ($meta['recipient_name'] ?? null),
                    'recipient_type' => $recipient['type'] ?? ($meta['recipient_type'] ?? null),
                    'recipient_id'   => $recipient['id'] ?? ($meta['recipient_id'] ?? null),
                ]);

                // Substituição de tags dinâmicas se fornecido nome
                $customMessage = $message;
                if (!empty($recipient['name'])) {
                    $customMessage = str_replace('{nome}', $recipient['name'], $customMessage);
                    $customMessage = str_replace('[NOME]', $recipient['name'], $customMessage);
                }
            } else {
                $phone = (string) $recipient;
                $recipientMeta = $meta;
                $customMessage = $message;
            }

            if (!$phone) {
                $failed++;
                continue;
            }

            $ok = $this->send($phone, $customMessage, $recipientMeta);
            if ($ok) {
                $sent++;
            } else {
                $failed++;
            }
        }

        return [
            'total'  => $total,
            'sent'   => $sent,
            'failed' => $failed,
        ];
    }

    /**
     * Reenviar uma mensagem já registrada no banco.
     */
    public function resend(SmsMessage $smsMessage): bool
    {
        $formattedPhone = $this->formatPhone($smsMessage->phone);
        $smsMessage->update([
            'status' => 'pending',
            'error_message' => null,
        ]);

        try {
            $success = $this->provider->send($formattedPhone, $smsMessage->message);

            if ($success) {
                $smsMessage->update([
                    'status'  => 'sent',
                    'sent_at' => now(),
                    'error_message' => null,
                ]);
            } else {
                $smsMessage->update([
                    'status'        => 'failed',
                    'error_message' => 'Provedor ' . $this->driver . ' retornou falha na tentativa de reenvio.',
                ]);
            }

            return $success;
        } catch (Exception $e) {
            report($e);
            $smsMessage->update([
                'status'        => 'failed',
                'error_message' => 'Exceção no reenvio: ' . $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Formatação básica de telefone para Moçambique (+258...).
     */
    public function formatPhone(string $phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', $phone);

        if ($digits !== '' && strpos($digits, '258') !== 0) {
            $digits = '258' . $digits;
        }

        return $digits;
    }
}
