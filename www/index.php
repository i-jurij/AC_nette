<?php

declare(strict_types=1);

// require __DIR__ . '/../vendor/autoload.php';
if (@!include __DIR__.'/../vendor/autoload.php') {
    exit('Install Nette using `composer update`');
}

$configurator = App\Bootstrap::boot();
$container = $configurator->createContainer();
$application = $container->getByType(Nette\Application\Application::class);
$application->run();
