<?php

declare(strict_types=1);

namespace App\UI\Home\Client\Offer;

use App\Model\OfferFacade;
use App\UI\Accessory\IsBot;
use Nette\Application\UI\Form;
use stdClass;
use Nette\Utils\Paginator;

/**
 * @property OfferTemplate $template
 */
final class OfferPresenter extends \App\UI\Home\BasePresenter
{
    use \App\UI\Accessory\RequireLoggedUser;

    protected int $items_on_page_paginator = 10;

    public function __construct(
        protected OfferFacade $of,
    ) {
        parent::__construct();
        $this->onStartup[] = function () {
            if (IsBot::check()) {
                $this->redirect(':Home:');
            }
        };
    }
    public function renderDefault(int $page = 1)
    {
        $formdata = new stdClass;
        $formdata->client_id = $this->getUser()->getId();

        $offersCount = $this->of->offersCount(location: $this->locality, form_data: $formdata);
        $paginator = new Paginator();
        $paginator->setItemCount($offersCount);
        $paginator->setItemsPerPage($this->items_on_page_paginator);
        $paginator->setPage($page);
        $this->template->paginator = $paginator;

        $this->template->offers = $this->of->getOffers(form_data: $formdata);

        //$this->template->backlink = $this->storeRequest();
    }

    public function renderEdit(int $o, int $c)
    {
        if ($this->getUser()->isLoggedIn() && $this->getUser()->getId() == $c) {
            $fd = new stdClass;
            $fd->id = $o;
            $fd->client_id = $c;
            $this->template->offers = $this->of->getOffers(form_data: $fd);
        }
    }

    public function handleRemove(int $o, int $c)
    {
        if ($this->getUser()->isLoggedIn() && $this->getUser()->getId() == $c) {
            $res = $this->of->remove($o);
            if ($res > 0) {
                $this->redirect('this');
            }
        }
    }
}

class OfferTemplate extends \App\UI\Home\BaseTemplate
{
    public array $offers;
    public Paginator $paginator;
    public string $backlink;
}
