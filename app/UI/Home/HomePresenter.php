<?php

declare(strict_types=1);

namespace App\UI\Home;

// use App\Model\PageFacade;
use App\Model\OfferFacade;
use Ijurij\Geolocation\Lib\Session;
use Nette\Database\Connection;
use Nette\Database\Explorer;

/**
 * @property HomeTemplate $template
 */
final class HomePresenter extends BasePresenter
{
    protected string $type;
    protected array $location;

    public function __construct(
        private Explorer $db,
        private OfferFacade $offers,
    ) {
        parent::__construct();
        $type = Session::get('offertype');
        $this->type = (!empty($type)) ? $type : '';
    }

    public function actionSaveToBackend()
    {
        $data = $this->getData($this->type, $this->locality);

        $this->sendJson($data);
    }

    public function renderDefault()
    {
        $this->template->data = $this->getData($this->type, $this->locality);
        $this->template->offers_type = $this->type;
    }

    #[Requires(methods: 'POST', sameOrigin: true)]
    public function handleFilter(): void
    {
        $httpRequest = $this->getHttpRequest();
        $offertype = $httpRequest->getPost('offertype');
        $this->type = (!empty($offertype)) ? $offertype : '';
        Session::destroy('offertype');
        Session::set('offertype', $offertype);
        $this->redirect('this');
    }

    private function getData(string $type = '', array $location = [], int $offset = 0, int $length = 25): string
    {
        $loc_id = (!empty($location['city'])) ? $this->getLocationId($location) : '';
        $loc_cond = (!empty($loc_id)) ? 'location' : 'location<>';
        $sql_offer_method = 'get'.\ucfirst($type).'s';
        $sql_type_param = $type.'offer';
        $string = '';

        if (!empty($type)) {
            $offer = (!empty($location['city']))
            ?
            $this->db->table('offer')
                ->where('offers_type', $sql_type_param)
                ->where($loc_cond, $loc_id)
                ->where('end_time >', 'CURRENT_TIMESTAMP')
                ->order('created_at DESC')
                ->fetchAll()
            :
            $offer = $this->offers->{$sql_offer_method}();
        } else {
            $offer = (!empty($location['city']))
            ?
             $this->db->table('offer')
                ->where($loc_cond, $loc_id)
                ->where('end_time >', 'CURRENT_TIMESTAMP')
                ->order('created_at DESC')
                ->fetchAll()
            :
            $offer = $this->offers->get();
        }

        $string = $this->ts($offer);

        return $string;
    }

    private function getLocationId(array $location): int
    {
        $path_to_geo_db = realpath(APPDIR
            .DIRECTORY_SEPARATOR
            .'..'
            .DIRECTORY_SEPARATOR
            .'vendor'
            .DIRECTORY_SEPARATOR
            .'i-jurij'
            .DIRECTORY_SEPARATOR
            .'geolocation'
            .DIRECTORY_SEPARATOR
            .'src'
            .DIRECTORY_SEPARATOR
            .'sqlite'
            .DIRECTORY_SEPARATOR
            .'geolocation.db');

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
        $string = '<div class="flexx one two-600 three-1000 four-1400 five-2000 center ">';
        foreach ($data as $v) {
            $string .=
                '<div>
                <article class="card">
                <p> ID: '.$v->id.'</p>
                <p> Client: '.$v->client_id.'</p>
                <p> Type: '.$v->offers_type.'</p>
                <p> Location: '.$v->location.'</p>
                <p> Services: '.$v->services.'</p>
                <p> Price: '.$v->price.'</p>
                <p> Message: '.$v->message.'</p>
                <p> Updated_at: '.$v->updated_at
                .'</article></div>'
            ;
        }
        $string .= '</div>';

        return $string;
    }
}
class HomeTemplate extends BaseTemplate
{
    public string $data;
    public string $offers_type;
}
