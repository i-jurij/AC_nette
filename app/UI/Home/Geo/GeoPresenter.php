<?php

declare(strict_types=1);

namespace App\UI\Home\Geo;

/**
 * @property GeoTemplate $template
 */
final class GeoPresenter extends \App\UI\BasePresenter
{
    public function actionFromCoord(): void
    {
        $fromCoord = new \Geolocation\Php\Fetchcontroller();
        $this->sendJson($fromCoord->fromCoord());
    }

    public function actionFromDb(): void
    {
        $fromDb = new \Geolocation\Php\Fetchcontroller();
        $this->sendJson($fromDb->getAll());
    }
}
