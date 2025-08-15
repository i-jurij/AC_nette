<?php

namespace App\UI\Home;

// use App\Model\PageFacade;
use App\Model\OfferFacade;
use App\Model\ServiceFacade;
use \App\Model\ChatFacade;
use App\UI\Accessory\FormFactory;
use Ijurij\Geolocation\Lib\Csrf;
use Ijurij\Geolocation\Lib\Session;
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
        private OfferFacade $offers,
        private ServiceFacade $services,
        private FormFactory $formFactory,
        protected ChatFacade $chat
    ) {
        parent::__construct($this->chat);
        $this->form_data = new \stdClass();
        $this->form_data->offertype = 'all';
        $this->form_data->order_by = 'end_time';
        $this->form_data->order_type = 'DESC';
        $this->form_data->moderated = 1;
        if (Session::has('form_data')) {
            $newFD = \json_decode(Session::get('form_data'), true);
            foreach ($newFD as $key => $value) {
                $this->form_data->{$key} = $value;
            }
        }
    }

    public function actionSaveToBackend()
    {
        $this->setOfferPaginator(1);

        $offers = $this->offers->getOffers($this->locality, $this->template->paginator->getLength(), $this->template->paginator->getOffset(), $this->form_data);
        $latte = $this->template->getLatte();
        $params = [
            'offers' => $offers,
            'paginator' => $this->template->paginator,
            'user' => $this->user,
            'baseUrl' => $this->template->baseUrl,
        ];
        $template = APPDIR . DIRECTORY_SEPARATOR . 'UI' . DIRECTORY_SEPARATOR . 'shared_templates' . DIRECTORY_SEPARATOR . 'offers_list.latte';
        $output = $latte->renderToString($template, $params);

        $this->sendJson($output);
    }

    public function renderDefault(int $page = 1)
    {
        $this->setOfferPaginator($page);

        $this->template->form_data = $this->form_data;
        $this->template->service_list = $this->services->getAllServices();

        $price = $this->offers->priceMinMax();
        $this->template->price = [
            'price_min' => $price->price_min,
            'price_max' => $price->price_max,
        ];

        $this->template->offers = $this->offers->getOffers($this->locality, $this->template->paginator->getLength(), $this->template->paginator->getOffset(), $this->form_data);
        $this->template->csrf_name = Csrf::$token_name;
        $this->template->csrf = Csrf::getToken();
    }

    protected function setOfferPaginator(int $page)
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
        if (Csrf::isValid() && Csrf::isRecent()) {
            $httpRequest = $this->getHttpRequest();
            Session::set('form_data', \json_encode($httpRequest->getPost()));
            $this->redirect('this');
        } else {
            $this->error();
        }
    }
}
class HomeTemplate extends BaseTemplate
{
    public array $offers;
    public object $form_data;
    public Paginator $paginator;
    public array $service_list;
    public array $price;
    public string $csrf_name;
    public string $csrf;
}
