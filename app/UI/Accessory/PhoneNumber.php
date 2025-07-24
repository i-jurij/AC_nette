<?php

namespace App\UI\Accessory;

final class PhoneNumber
{
    // for all RF phone (mobile and home in various formats)
    // public const PHONE_REGEX = "(\+?7|8)?\s?[\(]{0,1}?\d{3}[\)]{0,1}\s?[\-]{0,1}?\d{1}\s?[\-]{0,1}?\d{1}\s?[\-]{0,1}?\d{1}\s?[\-]{0,1}?\d{1}\s?[\-]{0,1}?\d{1}\s?[\-]{0,1}?\d{1}\s?[\-]{0,1}?\d{1}\s?[\-]{0,1}?";
    // RF phone in mobile format
    // public const PHONE_REGEX = "(\+7|8)\s?[\(]{0,1}?\d{3}[\)]{0,1}\s?[\-]{0,1}?\d{1}\s?[\-]{0,1}?\d{1}\s?[\-]{0,1}?\d{1}\s?[\-]{0,1}?\d{1}\s?[\-]{0,1}?\d{1}\s?[\-]{0,1}?\d{1}\s?[\-]{0,1}?\d{1}\s?[\-]{0,1}?";
    // RF phone in international mobile format only with +7 at the start
    public const PHONE_REGEX = "(\+?7)\s?[\(]{0,1}?\d{3}[\)]{0,1}\s?[\-]{0,1}?\d{1}\s?[\-]{0,1}?\d{1}\s?[\-]{0,1}?\d{1}\s?[\-]{0,1}?\d{1}\s?[\-]{0,1}?\d{1}\s?[\-]{0,1}?\d{1}\s?[\-]{0,1}?\d{1}\s?[\-]{0,1}?";
    // SNG numbers // protected const PHONE_REGEX = "^((8|\+374|\+994|\+995|\+375|\+7|\+380|\+38|\+996|\+998|\+993)[\- ]?)?\(?\d{3,5}\)?[\- ]?\d{1}[\- ]?\d{1}[\- ]?\d{1}[\- ]?\d{1}[\- ]?\d{1}(([\- ]?\d{1})?[\- ]?\d{1})?$";

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

            $number = '+' . $sArea . ' (' . $sPrefix . ') ' . $sNumber1 . ' ' . $sNumber2 . ' ' . $sNumber3;

            return $number;
        } else {
            return $sPhone;
        }
    }

    public static function isValid($sPhone, $regex = self::PHONE_REGEX): bool
    {
        if (preg_match('/' . $regex . '/', $sPhone)) {
            return true;
        }

        return false;
    }
}
