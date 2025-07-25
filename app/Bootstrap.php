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
        $configurator->enableTracy($rootDir . '/log');

        $configurator->setTempDirectory($rootDir . '/temp');

        $configurator->createRobotLoader()
            ->addDirectory(__DIR__)
            ->register();

        $configurator->addConfig($rootDir . '/config/all_configs.neon');
        /*
        $configurator->addConfig($rootDir.'/config/common.neon');
        $configurator->addConfig($rootDir.'/config/db.neon');
        $configurator->addConfig($rootDir.'/config/services.neon');
        $configurator->addConfig($rootDir.'/config/webpack.neon');
        $configurator->addConfig($rootDir.'/config/own.neon');
        */

        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? true : false;
        $port = (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ? true : false;
        $protocol = ($https || $port) ? 'https://' : 'http://';
        $cur_url = $_SERVER['REQUEST_URI'] ?? false;
        $host = $_SERVER['HTTP_HOST'] ?? false;
        $cur_full_url = ((bool) $cur_url && (bool) $host) ? $protocol . $host . $cur_url : false;

        $check = ((bool) $cur_full_url) ? filter_var($cur_full_url, FILTER_VALIDATE_URL) : false;

        if ($check && \mb_stristr($check, '/admin') === false) {
            $configurator->addConfig($rootDir . '/config/auth_client.neon');
        } else {
            $configurator->addConfig($rootDir . '/config/auth_user.neon');
        }

        return $configurator;
    }
}
