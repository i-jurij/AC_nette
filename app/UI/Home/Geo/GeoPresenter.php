<?php

declare(strict_types=1);

namespace App\UI\Home\Geo;

use App\Model\Geo;
use Nette\Database\Connection;

/**
 * @property GeoTemplate $template
 */
final class GeoPresenter extends \App\UI\BasePresenter
{
    protected Geo $geo;

    // public function __construct(public Explorer $db)
    public function __construct(public Connection $db)
    {
        $this->geo = new Geo($this->db);
    }

    public function actionGetAll()
    {
        $this->sendJson($this->geo->getAll());
    }

    public function actionDistrict()
    {
        $this->sendJson($this->geo->district());
    }

    public function actionRegion($id)
    {
        $this->sendJson($this->geo->region($id));
    }

    public function actionCity($id)
    {
        $this->sendJson($this->geo->city($id));
    }

    // $id = long_lat from request (eg 44.000000_33.000000)
    public function actionLocationFromCoord($id)
    {
        $this->sendJson($this->geo->fromCoord($id));
    }
}
