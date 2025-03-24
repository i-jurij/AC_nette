<?php

declare(strict_types=1);

namespace App\UI\Home;

// use App\Model\PageFacade;
use Nette\Database\Connection;
use Nette\Database\Explorer;
use \App\Model\OfferFacade;

/**
 * @property HomeTemplate $template
 */
final class HomePresenter extends BasePresenter
{
    public function __construct(
        private Explorer $db,
        private OfferFacade $offers,
    ) {
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
        $string = '';

        if (!empty($location['city'])) {
            $loc_id = $this->getLocation($location);
            $offer = $this->db->table('offer')
                ->where('location', $loc_id)
                ->where('end_time >', 'CURRENT_TIMESTAMP')
                ->order('created_at DESC')
                ->fetchAll();
        } else {
            $offer = $this->offers->get();
        }

        $string = '<div class="flexx one two-600 three-1000 four-1400 five-2000 center ">';
        $string .= $this->ts($offer);
        $string .= '</div>';

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

        if (!empty($location['city']) && !empty($location['region'])) {
            $city = $location['city'];
            $region = $location['region'];

            $reg_sql = 'SELECT (`id`) FROM `geo_regions` WHERE `name` LIKE ?';
            $region_id = $sqlite->query($reg_sql, "%$region%")->fetchField();

            $res_id = $sqlite
                ->query('SELECT (`id`) FROM `geo_city` WHERE', [
                    'name LIKE' => "%$city%",
                    'region_id' => $region_id,
                ])
                ->fetchField();
        }

        if (!empty($location['city']) && empty($location['region'])) {
            $city = $location['city'];
            $loc_sql = 'SELECT (`id`) FROM `geo_city` WHERE `name` LIKE ?';
            $res_id = $sqlite->query($loc_sql, "%$city%")->fetchField();
        }

        return (int) $res_id;
    }
    private function ts($data)
    {
        $string = '';
        foreach ($data as $v) {
            $string .=
                '<div>
                <article class="card">
                <p> ID: ' . $v->id . '</p>
                <p> Client: ' . $v->client_id . '</p>
                <p> Location: ' . $v->location . '</p>
                <p> Services: ' . $v->services . '</p>
                <p> Price: ' . $v->price . '</p>
                <p> Message: ' . $v->message . '</p>
                <p> Updated_at: ' . $v->updated_at
                . '</article></div>'
            ;
        }
        return $string;
    }
}
class HomeTemplate extends BaseTemplate
{
    public string $data;
}
