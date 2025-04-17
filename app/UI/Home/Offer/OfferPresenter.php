<?php

declare(strict_types=1);

namespace App\UI\Home\Offer;

use App\Model\OfferFacade;
use App\Model\RatingFacade;
use App\UI\Accessory\FormFactory;
use Nette\Application\UI\Form;

/**
 * @property OfferTemplate $template
 */
final class OfferPresenter extends \App\UI\Home\BasePresenter
{
    use \App\UI\Accessory\RequireLoggedUser;

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
            $regex = '(^'.strval($id).'_){1}[0-9]+(.jpg|.png|.jpeg|.gif|.bmp|.webp)$';
            $this->template->offer_images = \App\UI\Accessory\FilesInDir::byRegex(WWWDIR.'/images/offers', "/$regex/");
            $this->template->backlink = $this->storeRequest();
        } else {
            $this->redirectPermanent(':Home:default');
        }
    }

    #[Requires(methods: 'POST', sameOrigin: true)]
    public function handleRatingForm(Form $form, $data)
    {
        $this->rf->add($data);
        $this->redirect('this');
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
