<?php

declare(strict_types=1);

namespace App\UI\Accessory;

class CheckUrl
{
    /**
     * function for url validation.
     *
     * @param string $url
     *
     * @return bool
     */
    public function getResponseCode($url)
    {
        $header = '';
        $options = [
            CURLOPT_URL => trim($url),
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
        ];

        $ch = curl_init();
        curl_setopt_array($ch, $options);
        curl_exec($ch);
        if (!curl_errno($ch)) {
            $header = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        }
        curl_close($ch);

        if ($header > 0 && $header < 400) {
            return true;
        } else {
            return false;
        }
    }
}
