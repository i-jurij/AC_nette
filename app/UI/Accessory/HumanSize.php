<?php

namespace App\UI\Accessory;

/**
 * Trait to change size into human readable.
 */
trait HumanSize
{
    public function formatBytes($bytes, $precision = 2)
    {
        if ($bytes > 0) {
            $i = floor(log($bytes) / log(1024));
            $sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

            return sprintf('%.02F', round($bytes / pow(1024, $i), $precision)) * 1 . ' ' . @$sizes[$i];
        } else {
            return 0;
        }
    }
}
