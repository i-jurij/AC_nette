<?php

declare(strict_types=1);

namespace App\UI\Home;

use Ijurij\Geolocation\Geolocation;

abstract class BasePresenter extends \App\UI\BasePresenter
{
    protected Geolocation $geo;
    protected array $locality;

    public function __construct()
    {
        parent::__construct();
        $this->geo = new Geolocation();
        $this->locality = $this->geo->getLocality();
    }

    public function beforeRender()
    {
        parent::beforeRender();

        $this->template->geo = $this->geo->run();
        $this->template->location = $this->locality;
    }
}

class BaseTemplate extends \App\UI\BaseTemplate
{
    public string $geo;
    public array $location;
}
