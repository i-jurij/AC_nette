<?php

declare(strict_types=1);

namespace App\UI\Accessory;

trait ScanDirRec
{
    public function scanDirRec($directory, $sub_folders = '')
    {
        $directory_tree = [];
        $directory = rtrim($directory, DIRECTORY_SEPARATOR);
        $directory_scan = scandir($directory);
        unset($directory_scan[0], $directory_scan[1]);

        foreach ($directory_scan as $key => $value) {
            $path = $directory.DIRECTORY_SEPARATOR.$value;
            if (is_dir($path)) {
                $ar = [
                    'path' => $path,
                    'name' => $value,
                    'kind' => 'directory',
                    'content' => $this->scanDirRec($path, $value),
                ];
                array_push($directory_tree, $ar);
            } elseif (is_file($path)) {
                $ar = [
                    'path' => $path,
                    'name' => $value,
                    'kind' => 'file',
                ];
                array_push($directory_tree, $ar);
            }
        }

        return $directory_tree;
    }
}
