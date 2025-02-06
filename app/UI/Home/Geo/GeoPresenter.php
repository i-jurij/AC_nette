<?php

declare(strict_types=1);

namespace App\UI\Home\Geo;

final class GeoPresenter extends \App\UI\BasePresenter
{
    public function actionJsFetch(): void
    {
        $geo = new \Ijurij\Geolocation\Geolocation();
        $this->sendJson($geo->run());
    }
}
