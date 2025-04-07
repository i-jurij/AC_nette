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
    protected int $items_on_page_paginator = 2;
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

        $offers = $this->offers->getOffers($this->locality, $this->template->paginator->getLength(), $this->template->paginator->getOffset(), $this->form_data)->fetchAll();
        $latte = $this->template->getLatte();
        $params = [
            'offers' => $offers,
            'paginator' => $this->template->paginator,
        ];
        $template = APPDIR.DIRECTORY_SEPARATOR.'UI'.DIRECTORY_SEPARATOR.'shared_templates'.DIRECTORY_SEPARATOR.'offers_list.latte';
        $output = $latte->renderToString($template, $params);

        $this->sendJson($output);
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

        $this->template->offers = $this->offers->getOffers($this->locality, $this->template->paginator->getLength(), $this->template->paginator->getOffset(), $this->form_data)->fetchAll();
    }

    protected function setPaginator(int $page)
    {
        $offersCount = $this->offers->offersCount(location: $this->locality, form_data: $this->form_data);
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
}
class HomeTemplate extends BaseTemplate
{
    public array $offers;
    public object $form_data;
    public Paginator $paginator;
    public array $service_list;
    public array $price;
}
