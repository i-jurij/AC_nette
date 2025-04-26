<?php

declare(strict_types=1);

namespace App\UI\Accessory\Moderating;

final class ModeratingText
{
    /**
     * Check string for url and non-normative lexic.
     *
     * @param string $string
     * @param string $continue
     * @param bool   $is_html
     * @param string $charset
     */
    public static function isTextBad($string, $continue = "\xe2\x80\xa6", $is_html = false, $charset = 'UTF-8'): bool
    {
        $res = true;

        $has_url = UrlInText::stringHasUrl($string);

        if ($has_url === false) {
            $ct = CensureText::parse(string: $string, delta: '0', continue: $continue, is_html: $is_html, replace: null, charset: $charset);
            if ($ct === false) {
                $res = false; // false, no bad
            }

            if (is_string($ct)) {
                $res = true; // true, bad
            }
        }

        return $res;
    }

    public static function cleanText($string, $delta = '0', $continue = "\xe2\x80\xa6", $is_html = false, $replace = '***', $charset = 'UTF-8'): bool|int|string|null
    {
        $ct = CensureText::parse(string: $string, delta: $delta, continue: $continue, is_html: $is_html, replace: $replace, charset: $charset);

        return $ct;
    }

    public static function replaceText($string, $delta = '0', $continue = "\xe2\x80\xa6", $is_html = false, $replace = null, $charset = 'UTF-8'): bool|int|string|null
    {
        $ct = CensureText::parse(string: $string, delta: $delta, continue: $continue, is_html: $is_html, replace: $replace, charset: $charset);

        return $ct;
    }
}
