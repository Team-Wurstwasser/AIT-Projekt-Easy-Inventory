<?php

namespace App\Helpers;

use App\Models\Werkzeug;

class Ean13
{
    public static function isValid(string $barcode): bool
    {
        if (!preg_match('/^\d{13}$/', $barcode)) {
            return false;
        }

        return self::checkDigit(substr($barcode, 0, 12)) === (int)$barcode[12];
    }

    public static function generate(): string
    {
        do {
            $base = '200' . str_pad((string)random_int(0, 999999999), 9, '0', STR_PAD_LEFT);
            $code = $base . self::checkDigit($base);
        } while (Werkzeug::whereKey($code)->exists());

        return $code;
    }

    private static function checkDigit(string $first12): int
    {
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int)$first12[$i] * ($i % 2 === 0 ? 1 : 3);
        }

        return (10 - ($sum % 10)) % 10;
    }
}
