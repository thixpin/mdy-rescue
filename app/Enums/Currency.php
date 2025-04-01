<?php

namespace App\Enums;

enum Currency: string
{
    case MMK = 'MMK';
    case USD = 'USD';
    case THB = 'THB';
    case SGD = 'SGD';

    public function label(): string
    {
        return match ($this) {
            self::MMK => 'မြန်မာငွေ',
            self::THB => 'Thai Baht',
            self::SGD => 'Singapore Dollar',
            self::USD => 'US Dollar',
        };
    }

    public function symbol(): string
    {
        return match ($this) {
            self::MMK => 'K',
            self::THB => '฿',
            self::SGD => 'S$',
            self::USD => '$',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(function ($currency) {
            return [$currency->value => $currency->label()];
        })->toArray();
    }

    public function format(float $amount): string
    {
        return $this->value === 'MMK' ?
            self::toMyanmarNumber($amount).'/-' :
            $this->value.' '.number_format($amount, 2).' '.$this->symbol();
    }

    public static function toMyanmarNumber(float $amount): string
    {
        $mmNumbers = [
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

        return str_replace(array_keys($mmNumbers), $mmNumbers, number_format($amount, 0));

    }
}
