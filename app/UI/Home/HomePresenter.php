<?php

declare(strict_types=1);

namespace App\UI\Home;

// use App\Model\PageFacade;
use App\Model\OfferFacade;
use Ijurij\Geolocation\Lib\Session;
use Nette\Database\Explorer;
use Nette\Utils\Paginator;

/**
 * @property HomeTemplate $template
 */
final class HomePresenter extends BasePresenter
{
    protected string $type;
    protected array $location;

    public function __construct(
        private Explorer $db,
        private OfferFacade $offers
    ) {
        parent::__construct();
        $type = Session::get('offertype');
        $this->type = (!empty($type)) ? $type : '';
    }

    public function actionSaveToBackend()
    {
        $data = $this->getData(type: $this->type, location: $this->locality);

        $this->sendJson($data);
    }

    public function renderDefault(int $page = 1)
    {
        $offersCount = $this->offers->offersCount(type: $this->type, location: $this->locality);
        $paginator = new Paginator();
        $paginator->setItemCount($offersCount);
        $paginator->setItemsPerPage(3);
        $paginator->setPage($page);

        $this->template->paginator = $paginator;
        $this->template->data = $this->getData($this->type, $this->locality, $paginator->getLength(), $paginator->getOffset());
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

    private function getData(string $type = '', array $location = [], int $limit = 1000, ?int $offset = null): string
    {
        $offer = $this->offers->getOffers($type, $location, $limit, $offset);
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
                <p> ID: ' . $v->id . '</p>
                <p> Client: ' . $v->client_id . '</p>
                <p> Type: ' . $v->offers_type . '</p>
                <p> Location: ' . $v->location . '</p>
                <p> Services: ' . $v->services . '</p>
                <p> Price: ' . $v->price . '</p>
                <p> Message: ' . $v->message . '</p>
                <p> Updated_at: ' . $v->updated_at
                . '</article></div>'
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
    public Paginator $paginator;
}
