<?php

namespace App\Helpers;

class Formatter
{
    public static function myanmarCurrency($amount)
    {
        $amount = number_format($amount, 0, '.', ',');
        $amount = self::convertToMmNumber($amount);
        $amount = 'အလှူငွေ ( '.$amount.'/- )';

        return $amount;
    }

    public static function convertToMmNumber($amount)
    {
        $numbers = [
            '0' => '၀',
            '1' => '၁',
            '2' => '၂',
            '3' => '၃',
            '4' => '၄',
            '5' => '၅',
            '6' => '၆',
            '7' => '၇',
            '8' => '၈',
            '9' => '၉',
        ];

        return str_replace(array_keys($numbers), $numbers, $amount);
    }
}
