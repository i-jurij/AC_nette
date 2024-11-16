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

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
        $cur_url = $_SERVER['REQUEST_URI'];
        $cur_full_url = $protocol.$_SERVER['HTTP_HOST'].$cur_url;
        $check = filter_var($cur_full_url, FILTER_VALIDATE_URL);

        if ($check && (bool) \mb_stristr($check, 'admin')) {
            $configurator->addConfig($rootDir.'/config/auth_user.neon');
        } else {
            $configurator->addConfig($rootDir.'/config/auth_client.neon');
        }

        return $configurator;
    }
}
