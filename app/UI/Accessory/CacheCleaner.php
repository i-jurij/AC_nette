<?php

declare(strict_types=1);

namespace App\UI\Accessory;

use Nette\Utils\Finder;

trait CacheCleaner
{
    use HumanSize;

    public function cleanCache($dirs): int
    {
        ['files' => $files, 'size' => $size] = $this->getFileList($dirs);
        $i = 0;
        foreach ($files as $file) {
            @unlink($file['file']);
            ++$i;
        }

        return $i;
    }

    /**
     * get list of files in directory recursive.
     *
     * @param string $dirs - path to directory
     */
    public function getFileList($dirs): array
    {
        $files = [];
        $size = 0;
        if (\is_array($dirs)) {
            foreach ($dirs as $dir) {
                $this->scan($dir, $size, $files);
            }
        }
        if (\is_string($dirs)) {
            $this->scan($dirs, $size, $files);
        }

        return [
            'files' => $files,
            'size' => $this->formatBytes($size),
        ];
    }

    /**
     * @param string $dir
     * @param array  $files
     * @param int    $size
     */
    private function scan($dir, &$size, &$files = [])
    {
        if (!is_dir($dir)) {
            return;
        }

        /** @var \SplFileInfo $file */
        foreach (Finder::findFiles('*')->in($dir) as $file) {
            $files[] = [
                'file' => $file->getRealPath(),
                'size' => $this->formatBytes($file->getSize()),
            ];
            $size += $file->getSize();
        }

        foreach (Finder::findDirectories('*')->in($dir) as $childDir) {
            $this->scan($childDir->getRealPath(), $size, $files);
        }
    }
}
