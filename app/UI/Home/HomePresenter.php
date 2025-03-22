<?php

declare(strict_types=1);

namespace App\UI\Home;

// use App\Model\PageFacade;
use Nette\Database\Connection;
use Nette\Database\Explorer;

/**
 * @property HomeTemplate $template
 */
final class HomePresenter extends BasePresenter
{
    public function __construct(public Explorer $db)
    {
        parent::__construct();
    }

    public function actionSaveToBackend()
    {
        $httpRequest = $this->getHttpRequest();

        $data = '';
        if (
            $httpRequest->isMethod('POST')
            && !empty($httpRequest->getPost('city'))
            && !empty($httpRequest->getPost('region'))
        ) {
            $city = filter_var($httpRequest->getPost('city'), FILTER_SANITIZE_SPECIAL_CHARS);
            $region = filter_var($httpRequest->getPost('region'), FILTER_SANITIZE_SPECIAL_CHARS);

            $location = [
                'city' => $city,
                'region' => $region,
            ];
            // code for saving user location to server
            // code for getting data by location
            $data .= $this->getData($location);
            // $this->sendJson($this->model->getData);
            // $data .= $city . '<br>' . $region . '<br>Content after city choice';
        }

        $this->sendJson($data);
    }

    public function renderDefault()
    {
        $this->template->data = $this->getData($this->template->location);
    }

    private function getData($location = []): string
    {
        if (!empty($location['city'])) {
            $loc_id = $this->getLocation($location);

            $data = $this->db->table('offer')
                ->where('offer.location', $loc_id)
                ->where('offer.end_time >', \time())
                ->fetchAll();
        } else {
            $data = $this->db->table('offer')->where('end_time >', \time())->fetchAll();
        }

        $string = '';
        foreach ($data as $v) {
            $string .= $v->id
                . ' ' .
                $v->offers_type
                . ' ' .
                $v->client_id
                . ' ' .
                $v->location
                . ' ' .
                $v->services
                . ' ' .
                $v->price
                . ' ' .
                $v->message
                . ' ' .
                $v->updated_at
            ;
        }
        return $string;
    }

    private function getLocation(array $location): int
    {
        $path_to_geo_db = realpath(APPDIR
            . DIRECTORY_SEPARATOR
            . '..'
            . DIRECTORY_SEPARATOR
            . 'vendor'
            . DIRECTORY_SEPARATOR
            . 'i-jurij'
            . DIRECTORY_SEPARATOR
            . 'geolocation'
            . DIRECTORY_SEPARATOR
            . 'src'
            . DIRECTORY_SEPARATOR
            . 'sqlite'
            . DIRECTORY_SEPARATOR
            . 'geolocation.db');

        $dsn = "sqlite:$path_to_geo_db";

        $sqlite = new Connection($dsn);
        /*
                if (!empty($location['city']) && !empty($location['region'])) {
                    $city = $location['city'];
                    $region = $location['region'];

                    $res_id = $sqlite
                        ->query('SELECT (`id`) FROM `geo_city` WHERE', [
                            '`geo_city`.`name` LIKE' => "%$city%",
                            '`region` LIKE' => "%$region%",
                        ])
                        ->fetch()
                        ->id;
                }
                        */
        $city = $location['city'];
        $loc_sql = 'SELECT (`id`) FROM `geo_city` WHERE `name` LIKE ?';
        $res_id = $sqlite->query($loc_sql, "%$city%")->fetchField();

        return (int) $res_id;
    }
}
class HomeTemplate extends BaseTemplate
{
    public string $data;
}
