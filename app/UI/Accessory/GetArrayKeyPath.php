<?php

namespace App\UI\Accessory;

trait GetArrayKeyPath
{
    public function getKeyPath($arr, $lookup)
    {
        if (array_key_exists($lookup, $arr)) {
            return [$lookup];
        } else {
            foreach ($arr as $key => $subarr) {
                if (is_array($subarr)) {
                    $ret = $this->getKeyPath($subarr, $lookup);

                    if ($ret) {
                        $ret[] = $key;

                        return $ret;
                    }
                }
            }
        }

        return null;
    }
}
