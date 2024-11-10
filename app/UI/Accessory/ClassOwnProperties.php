<?php

declare(strict_types=1);

namespace App\UI\Accessory;

class ClassOwnProperties
{
    public static function get(string $class): array
    {
        $reflect = new \ReflectionClass($class);
        $props = $reflect->getProperties();
        $ownProps = [];
        foreach ($props as $prop) {
            if ($prop->class === $class) {
                $ownProps[] = $prop->getName();
            }
        }

        return $ownProps;
    }
}
