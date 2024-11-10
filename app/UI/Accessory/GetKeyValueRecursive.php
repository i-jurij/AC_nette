<?php

declare(strict_types=1);

namespace App\UI\Accessory;

trait GetKeyValueRecursive
{
    public function getKeyValueRec(string $lookup, array $arr): array
    {
        $res = [];
        if (array_key_exists($lookup, $arr)) {
            $res = $arr[$lookup];
        } else {
            foreach ($arr as $subarr) {
                if (is_array($subarr)) {
                    $pre_res = $this->getKeyValueRec($lookup, $subarr);
                    if (!empty($pre_res)) {
                        $res = $pre_res;
                    }
                }
            }
        }

        return $res;
    }
}
