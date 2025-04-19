<?php

declare(strict_types=1);

namespace App\UI\Home\Offer;

use App\Model\OfferFacade;
use App\Model\RatingFacade;
use App\UI\Accessory\FormFactory;
use App\UI\Accessory\Ip;
use Nette\Application\UI\Form;

/**
 * @property OfferTemplate $template
 */
final class OfferPresenter extends \App\UI\Home\BasePresenter
{
    public function __construct(
        private OfferFacade $offers,
        private FormFactory $formFactory,
        private RatingFacade $rf
    ) {
        parent::__construct();
    }

    public function createComponentClientRatingForm()
    {
        $form = $this->formFactory->createClientRatingForm();
        $form->onSuccess[] = [$this, 'handleRatingForm'];

        return $form;
    }

    public function renderDefault(?int $id = null)
    {
        if (!empty($id) && is_integer($id)) {
            $form_data = new \stdClass();
            $form_data->id = $id;

            $this->template->offers = $this->offers->getOffers(form_data: $form_data);
            $regex = '(^' . strval($id) . '_){1}[0-9]+(.jpg|.png|.jpeg|.gif|.bmp|.webp)$';
            $this->template->offer_images = \App\UI\Accessory\FilesInDir::byRegex(WWWDIR . '/images/offers', "/$regex/");
            $this->template->backlink = $this->storeRequest();
        } else {
            $this->redirectPermanent(':Home:default');
        }
    }

    #[Requires(methods: 'POST', sameOrigin: true)]
    public function handleRatingForm(Form $form, $data)
    {
        $res = $this->rf->add($data);
        /*
        if ($res === false) {
            $ip = Ip::getIp();
            foreach ($ip['suspected'] as $k => $v) {
                $sus[] = "$k: $v";
            }
            foreach ($ip['network'] as $ka => $ve) {
                $all[] = "$ka: $ve";
            }
            $sus = \implode(', ', $sus);
            $all = \implode(', ', $all);
            $ip = $ip['ip'];
            $str = "ip $ip, suspected ip: $sus, all network ip: $all";

            \Tracy\Debugger::log("WARNING! \n
            Wrong data type from rating form on Offer page from \n
            $str; \n"
            .'UA: '.$_SERVER['HTTP_USER_AGENT'].".\n", \Tracy\Debugger::ERROR);
        }
        */
        $this->redirect('this');
    }

    #[Requires(methods: 'POST', sameOrigin: true)]
    public function actionJsFetchRatingForm()
    {
        $rating = 0;
        $httpRequest = $this->getHttpRequest();
        $data = $httpRequest->getPost();

        $d = new \Nette\Utils\ArrayHash();
        $d->client_id_who = (int) htmlspecialchars($data['client_id_who']);
        $d->client_id_to_whom = (int) htmlspecialchars($data['client_id_to_whom']);
        $d->rating_value = (int) htmlspecialchars($data['rating_value']);

        $res = $this->rf->add($d);
        if ($res) {
            $rating = $this->rf->get($d->client_id_to_whom);
        }

        $this->sendJson($rating);
        exit;
    }

    public function renderAdd()
    {
    }
}

class OfferTemplate extends \App\UI\Home\BaseTemplate
{
    public array $offers;
    public string $backlink;
    public array $offer_images;
}
