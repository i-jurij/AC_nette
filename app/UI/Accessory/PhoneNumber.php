<?php

declare(strict_types=1);

namespace App\UI\Accessory;

final class PhoneNumber
{
    public static function toDb($sPhone)
    {
        if (!isset($sPhone)) {
            return null;
        }

        return preg_replace('/[^0-9]+/', '', (string) $sPhone);
    }

    public static function fromDb($sPhone)
    {
        $number_pre = (string) self::toDb($sPhone) ?? '';
        $length = floor(log10((int) $number_pre) + 1);
        $first = mb_substr($number_pre, 0, 1);
        if ($length > 10 && $length < 12 && $first == 7) {
            $sArea = $first;
            $sPrefix = mb_substr($number_pre, 1, 3);
            $sNumber1 = mb_substr($number_pre, 4, 3);
            $sNumber2 = mb_substr($number_pre, 7, 2);
            $sNumber3 = mb_substr($number_pre, 9, 2);

            $number = '+'.$sArea.' ('.$sPrefix.') '.$sNumber1.' '.$sNumber2.' '.$sNumber3;

            return $number;
        } else {
            return $sPhone;
        }
    }
}
