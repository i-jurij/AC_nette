<?php
namespace App\UI\Accessory;

trait ErrorFlashMessage
{
    public function erMes(object $e)
    {
        return 'Error message: ' . $e->getMessage() . PHP_EOL
            . 'File: ' . $e->getFile() . PHP_EOL
            . 'Line: ' . $e->getLine() . PHP_EOL
            . 'Error code: ' . $e->getCode() . PHP_EOL
            . 'Trace: ' . $e->getTraceAsString() . PHP_EOL;
    }
}
