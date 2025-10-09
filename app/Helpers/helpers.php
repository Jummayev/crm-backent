<?php

declare(strict_types=1);

use Illuminate\Support\Carbon;

function dateFormat($date, $format = 'd.m.Y'): string
{
    try {
        if (empty($date)) {
            return '-';
        }

        return (new Carbon($date))->format($format);
    } catch (Exception $exception) {
        return $date;
    }
}

function _round($number, int $precision = 2): float
{
    if (! is_numeric($number)) {
        return 0;
    }

    $number = (string) $number;

    // BCMath yordamida yaxlitlash
    $multiplier = bcpow('10', (string) $precision, 0);
    $value = bcmul($number, $multiplier, 0);
    $value = bcadd($value, '0.5', 0); // Yaxlitlash uchun 0.5 qo'shamiz

    return (float) bcdiv($value, $multiplier, $precision);
}

function numberReadable($number, int $decimals = 2, string $dec_point = ',', string $thousands_sep = ' '): string
{
    if (! is_numeric($number)) {
        return "0{$dec_point}00";
    }

    // BCMath bilan aniq raqamni olish
    $rounded = _round($number, $decimals);

    // Butun va kasr qismlarini ajratish
    [$integer, $decimal] = explode('.', bcadd((string) $rounded, '0', $decimals));

    // Minglik ajratgichlar qo'shish
    $integer = strrev(implode($thousands_sep, str_split(strrev($integer), 3)));

    // Kasr qismini formatlash
    if ($decimals > 0 && $decimal !== null) {
        return $integer.$dec_point.str_pad($decimal, $decimals, '0');
    }

    return $integer;
}

function truncateToTwoDecimals($number): string
{
    if (! is_numeric($number)) {
        return '0.00';
    }

    $number = (string) $number;

    // BCMath bilan truncate (pastga yaxlitlash)
    $multiplied = bcmul($number, '100', 0);
    $truncated = bcdiv($multiplied, '100', 2);

    return $truncated;
}

function sendNotify(string $text, ?string $chat_id = null): void
{
    \App\Jobs\SendTelegramNotification::dispatch($text, $chat_id);
}

function generateRandomThreeDigitPalindrome(): int
{
    $firstDigit = rand(1, 9);
    $secondDigit = rand(0, 9);
    $palindrome = $firstDigit.$secondDigit.$firstDigit;

    return (int) $palindrome;
}

function phoneNumberVerify($phone): false|int
{
    $phone = (string) preg_replace('/[^0-9]/', '', $phone);
    if (strlen($phone) == 12) {
        if (\Illuminate\Support\Str::startsWith($phone, '998')) {
            return (int) $phone;
        }
    }

    return false;
}

function static_path(string $path = ''): string
{
    $path = ltrim($path, '/');

    return base_path("static/$path");
}

function private_path(string $path = ''): string
{
    $path = ltrim($path, '/');

    return base_path("static/private/$path");
}
