<?php

declare(strict_types=1);

namespace App\UI\Home;

abstract class BasePresenter extends \App\UI\BasePresenter
{
    public function beforeRender()
    {
        parent::beforeRender();
        $view = new \Geolocation\Php\View();
        $this->template->geo = $view->htmlOut();
        $this->template->location = $view->location;
    }
}

class BaseTemplate extends \App\UI\BaseTemplate
{
    public string $geo;
    public array $location;
}
