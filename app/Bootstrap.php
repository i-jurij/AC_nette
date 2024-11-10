<?php

declare(strict_types=1);

namespace App;

use Nette\Bootstrap\Configurator;

class Bootstrap
{
    public static function boot(): Configurator
    {
        $configurator = new Configurator();
        $rootDir = dirname(__DIR__);

        // $configurator->setDebugMode('secret@23.75.345.200'); // enable for your remote IP
        // $configurator->setDebugMode(false); // disable debug mode
        $configurator->enableTracy($rootDir.'/log');

        $configurator->setTempDirectory($rootDir.'/temp');

        $configurator->createRobotLoader()
            ->addDirectory(__DIR__)
            ->register();
        $configurator->setTimeZone('Europe/Moscow');

        $configurator->addConfig($rootDir.'/config/all_configs.neon');
        /*
        $configurator->addConfig($rootDir.'/config/common.neon');
        $configurator->addConfig($rootDir.'/config/db.neon');
        $configurator->addConfig($rootDir.'/config/services.neon');
        $configurator->addConfig($rootDir.'/config/webpack.neon');
        $configurator->addConfig($rootDir.'/config/own.neon');
        */

        return $configurator;
    }
}
