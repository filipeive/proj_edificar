<?php

namespace App\Models\Concerns;

trait NormalizesMozPhone
{
    protected function normalizeMozPhone(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $digits = preg_replace('/[^0-9]/', '', $value);
        if ($digits === '') {
            return null;
        }

        if (strpos($digits, '258') !== 0) {
            $digits = '258' . $digits;
        }

        return '+' . $digits;
    }
}
