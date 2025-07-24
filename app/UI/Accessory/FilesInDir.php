<?php

namespace App\UI\Accessory;

class FilesInDir
{
    /**
     * get file from dir by regex
     * $regex = '/(.jpg|.png|.jpeg|.gif|.bmp)/'; - only images eg
     * $regex = '/(^4_){1}[0-9]+(.jpg|.png|.jpeg|.gif|.bmp)$/'; - for images with name eg 4_2.jpg.
     */
    public static function byRegex($dir_path, $regex): array
    {
        $files = \scandir($dir_path);
        $output = [];
        foreach ($files as $file) {
            if (\preg_match($regex, \mb_strtolower($file, \mb_detect_encoding($file)))) {
                $output[] = $file;
            }
        }

        return $output;
    }

    /**
     * function find file by name without extension.
     *
     * @param $path     - directory
     * @param $filename - name without extension
     *
     * @return string|bool path to file or boolean false
     */
    public static function byFilename($path, $filename)
    {
        if (\is_readable($path)) {
            $files = \scandir($path);
            if (!empty($files)) {
                foreach ($files as $k => $v) {
                    $fname = \pathinfo($v, PATHINFO_FILENAME);
                    $only_name[$k] = $fname;
                }
                $name_key_name = \array_search($filename, $only_name);
                if (!empty($name_key_name)) {
                    return $files[$name_key_name];
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @param string $dir - dir for scan
     * @param string $ext - extension of files eg 'png' or 'png, webp, jpg'
     *
     * @return array basename of files
     */
    public static function filesInDirIter($dir, $ext = ''): array
    {
        if (\file_exists($dir) && \is_dir($dir) && \is_readable($dir)) {
            foreach (new \DirectoryIterator($dir) as $fileInfo) {
                if ($fileInfo->isDot()) {
                    continue;
                }
                if (empty($ext)) {
                    $files[] = $fileInfo->getBasename();
                } else {
                    $arr = \explode(',', $ext);
                    foreach ($arr as $value) {
                        $extt = \mb_strtolower(\ltrim(\trim($value), '.'), \mb_detect_encoding($value));
                        if ($extt === $fileInfo->getExtension()) {
                            $files[] = $fileInfo->getBasename();
                        }
                    }
                }
            }
        } else {
            throw new \Exception("Directory $dir not exist or not readable");
        }

        return $files;
    }

    /**
     * @param string $path - dir for scan
     * @param string $ext  - extension of files eg 'png' or 'png, webp, jpg'
     *
     * @return array path to files
     */
    public static function filesInDirScan($path, $ext = ''): array
    {
        $files = [];
        if (\file_exists($path)) {
            $f = \scandir($path);
            foreach ($f as $file) {
                if (\is_dir($file)) {
                    continue;
                }
                if (empty($ext)) {
                    $files[] = $file;
                } else {
                    $arr = \explode(',', $ext);
                    foreach ($arr as $value) {
                        $extt = \mb_strtolower(\trim($value), \mb_detect_encoding($value));
                        if ($extt === \mb_strtolower(\pathinfo($file, PATHINFO_EXTENSION), \mb_detect_encoding($value))) {
                            $files[] = $file;
                        }
                    }
                }
            }
        }

        return $files;
    }
}
