<?php

declare(strict_types=1);

namespace App\UI\Home;

// use App\Model\PageFacade;
use App\Model\OfferFacade;
use App\Model\ServiceFacade;
use Ijurij\Geolocation\Lib\Session;
use Nette\Database\Explorer;
use Nette\Utils\Paginator;

/**
 * @property HomeTemplate $template
 */
final class HomePresenter extends BasePresenter
{
    protected object $form_data;
    protected int $items_on_page_paginator = 20;
    private array $service_list = [];

    public function __construct(
        private Explorer $db,
        private OfferFacade $offers,
        private ServiceFacade $services,
    ) {
        parent::__construct();
        $this->form_data = new \stdClass();
        $this->form_data->offertype = 'all';
        $this->form_data->order_by = 'end_time';
        $this->form_data->order_type = 'DESC';
        if (Session::has('form_data')) {
            $newFD = \unserialize(Session::get('form_data'));
            foreach ($newFD as $key => $value) {
                $this->form_data->{$key} = $value;
            }
        }
    }

    public function actionSaveToBackend()
    {
        $data = $this->getData(location: $this->locality, limit: 0, offset: 0, form_data: $this->form_data);

        $this->sendJson($data);
    }

    public function renderDefault(int $page = 1)
    {
        $offersCount = $this->offers->offersCount(location: $this->locality);
        $paginator = new Paginator();
        $paginator->setItemCount($offersCount);
        $paginator->setItemsPerPage($this->items_on_page_paginator);
        $paginator->setPage($page);

        $this->template->paginator = $paginator;

        $fm = (!empty($this->form_data)) ? $this->form_data : null;
        $this->template->data = $this->getData($this->locality, $paginator->getLength(), $paginator->getOffset(), $fm);
        $this->template->form_data = $this->form_data;
        $this->template->service_list = $this->services->getAllServices();

        $this->template->price = [
            'price_min' => $this->offers->priceMinMax()->price_min,
            'price_max' => $this->offers->priceMinMax()->price_max,
        ];
    }

    #[Requires(methods: 'POST', sameOrigin: true)]
    public function handleFilterphp(): void
    {
        $httpRequest = $this->getHttpRequest();
        Session::set('form_data', \serialize($httpRequest->getPost()));

        $this->redirect('this');
    }

    private function getData(array $location = [], int $limit = 1000, ?int $offset = null, ?object $form_data = null): string
    {
        $offer = $this->offers->getOffers($location, $limit, $offset, $form_data)->fetchAll();

        $string = $this->ts($offer);

        return $string;
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
                <p> Price: '.$v->price.'</p>
                <p> Message: '.$v->message.'</p>
                <p> Updated_at: '.$v->updated_at.'</p>
                <p> End_time: '.$v->end_time.'</p>
                </article>
                </div>'
            ;
        }
        $string .= '</div>';

        return $string;
    }
}
class HomeTemplate extends BaseTemplate
{
    public string $data;
    public object $form_data;
    public Paginator $paginator;
    public array $service_list;
    public array $price;
}
