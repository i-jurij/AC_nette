<?php

namespace App\UI\Accessory;

class FormatPrice
{
    /**
     * echo format(1103.50);  // if $unit = 'руб.' -> 1 103,50 руб.
     * @param float $price
     * @param string $unit
     * @return string
     */
    public static function format(float $price, string $unit = ''): string
    {
        if ($price > 0) {
            $price = number_format($price, 2, ',', ' ');
            $price = str_replace(',00', '', $price);

            if (!empty($unit)) {
                $price .= $unit;
            }
        } else {
            $price = 'Нет в наличии';
        }

        return $price;
    }
}