<?php

namespace App\UI\Accessory;

trait ArrayKeyExistRecursive
{
    public function findKey($array, $keySearch)
    {
        foreach ($array as $key => $item) {
            if ($key == $keySearch) {
                return true;
            } elseif (is_array($item) && $this->findKey($item, $keySearch)) {
                return true;
            }
        }

        return false;
    }
}
