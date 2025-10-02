<?php

declare(strict_types=1);

namespace App\Helpers;

class Helper
{
    public static function getMonthName($state): string
    {
        return self::getMonth()[$state] ?: '--';
    }

    public static function getMonth(): array
    {
        return [
            1 => __('Yanvar'),
            2 => __('Fevral'),
            3 => __('Mart'),
            4 => __('Aprel'),
            5 => __('May'),
            6 => __('Iyun'),
            7 => __('Iyul'),
            8 => __('Avgust'),
            9 => __('Sentyabr'),
            10 => __('Oktyabr'),
            11 => __('Noyabr'),
            12 => __('Dekabr'),
        ];

    }

    public static function passportSeries(): array
    {
        return [
            'AD' => 'AD',
            'AA' => 'AA',
            'AB' => 'AB',
            'AC' => 'AC',
            'KA' => 'KA',
            'XS' => 'XS',
            'AE' => 'AE',
        ];
    }

    public static function validPhonePrefixes(): array
    {
        // List of valid Uzbekistan mobile prefixes
        // https://my.eskiz.uz/sms/prices
        return [
            '99890', // Beeline GSM
            '99891', // Beeline GSM
            '99899', // Uzmobile GSM
            '99877', // Uzmobile GSM
            '99870', // Uzmobile GSM
            '99895', // Uzmobile CDMA
            '99897', // MobiUz GSM
            '99888', // MobiUz GSM
            '99887', // MobiUz GSM
            '99893', // Ucell GSM
            '99894', // Ucell GSM
            '99850', // Ucell GSM
            '99833', // Humans GSM
            '99820', // OQ GSM
            '99898', // Perfectum CDMA
            '99871', // Uzmobile
        ];
    }
}
