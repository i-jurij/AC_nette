<?php

declare(strict_types=1);

namespace App\UI\Home;

abstract class BasePresenter extends \App\UI\BasePresenter
{
    public function beforeRender()
    {
        parent::beforeRender();
        $geo = new \Ijurij\Geolocation\Geolocation();

        $this->template->geo = $geo->run();
        $this->template->location = $geo->getLocality();
    }
}

class BaseTemplate extends \App\UI\BaseTemplate
{
    public string $geo;
    public array $location;
}
