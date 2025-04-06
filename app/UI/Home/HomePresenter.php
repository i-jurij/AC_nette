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
        $this->setPaginator(1);

        $offer = $this->offers->getOffers($this->locality, $this->template->paginator->getLength(), $this->template->paginator->getOffset(), $this->form_data)->fetchAll();
        $data = $this->ts_js($offer);

        $this->sendJson($data);
    }

    public function renderDefault(int $page = 1)
    {
        $this->setPaginator($page);

        $this->template->form_data = $this->form_data;
        $this->template->service_list = $this->services->getAllServices();

        $this->template->price = [
            'price_min' => $this->offers->priceMinMax()->price_min,
            'price_max' => $this->offers->priceMinMax()->price_max,
        ];

        $offer = $this->offers->getOffers($this->locality, $this->template->paginator->getLength(), $this->template->paginator->getOffset(), $this->form_data)->fetchAll();
        $this->template->data = $this->ts_js($offer);
    }

    protected function setPaginator(int $page)
    {
        $offersCount = $this->offers->offersCount(location: $this->locality);
        $paginator = new Paginator();
        $paginator->setItemCount($offersCount);
        $paginator->setItemsPerPage($this->items_on_page_paginator);
        $paginator->setPage($page);

        $this->template->paginator = $paginator;
    }

    #[Requires(methods: 'POST', sameOrigin: true)]
    public function handleFilterphp(): void
    {
        $httpRequest = $this->getHttpRequest();
        Session::set('form_data', \serialize($httpRequest->getPost()));

        $this->redirect('this');
    }

    private function ts_js($data)
    {
        $string = '<div class="flexx one two-600 three-1000 four-1400 five-2000 center ">';
        foreach ($data as $v) {
            $string .=
                '<div>
                <article class="card">
                <p> ID: ' . $v->id . '</p>
                <p> Client: ' . $v->client_id . '</p>
                <p> Type: ' . $v->offers_type . '</p>
                <p> Location: ' . $v->location . '</p>
                <p> Price: ' . $v->price . '</p>
                <p> Message: ' . $v->message . '</p>
                <p> Updated_at: ' . $v->updated_at . '</p>
                <p> End_time: ' . $v->end_time . '</p>
                </article>
                </div>'
            ;
        }
        $string .= '</div>';

        $first = '';
        if (!$this->template->paginator->isFirst()) {
            $first = '
                <a class="pseudo button" href="' . $this->link(':Home:default', 1) . '">1</a>
                <a class="pseudo button" href="' . $this->link(':Home:default', $this->template->paginator->getPage() - 1) . '">&nbsp;&#60;&nbsp;</a>';
        }

        $last = '';
        if (!$this->template->paginator->isLast()) {
            $last = '<a class="pseudo button" href="' . $this->link(':Home:default', $this->template->paginator->getPage() + 1) . '">&nbsp;&#62;&nbsp;</a>
                <a class="pseudo button" href="' . $this->link(':Home:default', $this->template->paginator->getPageCount()) . '">'
                . $this->template->paginator->getPageCount() .
                '</a>';
        }

        $string .= '
            <div class="pagination">'
            . $first .
            '&nbsp;Стр.&nbsp;' . $this->template->paginator->getPage() . '&nbsp;из&nbsp;' . $this->template->paginator->getPageCount() . '&nbsp;'
            . $last .
            '</div>
        ';

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
