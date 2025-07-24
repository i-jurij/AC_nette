<?php

namespace App\UI\Accessory;

trait ClearFile
{
    /**
     * @param string $path_to_file   - path to file
     * @param int    $keep_num_lines - the number of lines to save, starting from the end of the file, default is 10
     */
    public static function clearFile($path_to_file, $keep_num_lines = 10): bool
    {
        // clear log file if filetime > 1 week, but leave the last seven lines
        if (file_exists($path_to_file)) {
            if (is_file($path_to_file)) {
                $lines = file($path_to_file); // reads the file into an array by line
                $keep = (!empty($keep_num_lines)) ? array_slice($lines, -$keep_num_lines) : '';
                if (file_put_contents($path_to_file, $keep) !== false) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param string $log_folder     - path to logs folder
     * @param int    $keep_num_lines - the number of lines to save, starting from the end of the file
     */
    public static function clearAllFilesInDir($log_folder, $keep_num_lines = 0): bool
    {
        // clear log file if filetime > 1 week, but leave the last seven lines
        if (file_exists($log_folder)) {
            foreach (new \DirectoryIterator($log_folder) as $fileInfo) {
                if ($fileInfo->isDot() or $fileInfo->isDir()) {
                    continue;
                }
                if ($fileInfo->isFile()) {
                    $lines = file($fileInfo->getPathname()); // reads the file into an array by line
                    $keep = (!empty($keep_num_lines)) ? array_slice($lines, -$keep_num_lines) : '';
                    if (file_put_contents($fileInfo->getPathname(), $keep) === false) {
                        return false;
                    } else {
                        return true;
                    }
                }
            }
        }
    }
}
