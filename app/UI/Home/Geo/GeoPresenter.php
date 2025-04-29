<?php

declare(strict_types=1);

namespace App\UI\Home\Geo;

use \Ijurij\Geolocation\Geolocation;
use App\UI\Accessory\Location\Location;

final class GeoPresenter extends \App\UI\BasePresenter
{
    protected Geolocation $geo;

    public function actionJsFetch(): void
    {
        $this->geo = new Geolocation();
        $this->sendJson($this->geo->run());
    }

    public function actionAllLocations()
    {
        $loc = new Location;
        $all = $loc->list();
        $this->sendJson($all);
    }

    public function actionDistricts()
    {
        $loc = new Location;
        $dis = $loc->getDistricts();
        $this->sendJson($dis);
    }
    public function getRegions(int $district_id)
    {
        $loc = new Location;
        $reg = $loc->getRegions($district_id);
        $this->sendJson($reg);
    }

    public function actionCities(int $region_id): void
    {
        $loc = new Location;
        $cities = $loc->getCities($region_id);
        $this->sendJson($cities);
    }

}
