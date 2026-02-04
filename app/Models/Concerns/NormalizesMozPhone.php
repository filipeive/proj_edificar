<?php

namespace App\Models\Concerns;

trait NormalizesMozPhone
{
    protected function normalizeMozPhone(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Remover tudo que não for dígito
        $digits = preg_replace('/[^0-9]/', '', $value);

        // Se começar com 00, remover (prefixo internacional alternativo ao +)
        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        if ($digits === '') {
            return null;
        }

        // Se tiver 9 dígitos, assumimos que é um número local de Moçambique e adicionamos 258
        if (strlen($digits) === 9) {
            $digits = '258' . $digits;
        }

        return '+' . $digits;
    }
}
