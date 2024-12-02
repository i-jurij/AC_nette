<?php

declare(strict_types=1);

namespace App\UI\Home;

use App\UI\Accessory\Geo;

abstract class BasePresenter extends \App\UI\BasePresenter
{
    public function beforeRender()
    {
        parent::beforeRender();
        // get location of page visitor
        // $location = Geo::sypexGeo();
        $location = Geo::geoPlugin();
        // ////////////////////////////////////
        if (isset($location['city'])) {
            $this->template->city_from_back = $location['city'];
        }
        if (isset($location['region'])) {
            $this->template->region = $location['region'];
        }
    }
}

class BaseTemplate extends \App\UI\BaseTemplate
{
    public string $city_from_back;
    public string $region;
}
