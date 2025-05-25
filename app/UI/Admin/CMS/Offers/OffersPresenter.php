<?php

declare(strict_types=1);

namespace App\UI\Admin\CMS\Offers;

use App\Model\OfferFacade;
use App\Model\ServiceFacade;
use \App\Model\ChatFacade;
use App\UI\Accessory\IsBot;
use Nette\Application\UI\Form;
use Nette\Utils\Paginator;
use App\UI\Accessory\Location\Location;
use Nette\Forms\Container;
use App\UI\Accessory\PhoneNumber;
use Nette\Http\FileUpload;
use App\UI\Accessory\Moderating\ModeratingText;
use \Nette\Utils\ArrayHash;
use Nette\Utils\Image;
use Nette\Utils\ImageColor;
use Nette\Utils\ImageType;
use Nette\Utils\FileSystem;
use Nette\Utils\Finder;
use App\UI\Accessory\RequireLoggedClient;
use stdClass;

final class OffersPresenter extends \App\UI\Admin\BasePresenter
{
    protected int $items_on_page_paginator = 10;

    public function __construct(
        protected OfferFacade $of,
        protected ServiceFacade $sf,
        protected ChatFacade $chat
    ) {
        parent::__construct();
    }
    public function renderDefault(int $page = 1)
    {
        if (!$this->getUser()->isAllowed('Orders', '')) {
            $this->error('Forbidden', 403);
        }
        $this->setOfferPaginator($page);
        $this->template->service_list = $this->sf->getAllServices();
        $this->template->offers = $this->of->getOffers(limit: $this->template->paginator->getLength(), offset: $this->template->paginator->getOffset());
    }

    protected function setOfferPaginator(int $page)
    {
        $offersCount = $this->of->offersCount();
        $this->template->offersCount = $offersCount;
        $paginator = new Paginator();
        $paginator->setItemCount($offersCount);
        $paginator->setItemsPerPage($this->items_on_page_paginator);
        $paginator->setPage($page);

        $this->template->paginator = $paginator;
    }

    #[Requires(methods: 'POST', sameOrigin: true)]
    public function renderByClient(int $id): void
    {

    }

    #[Requires(sameOrigin: true)]
    public function renderImageEdit(int $id): void
    {

    }

    #[Requires(sameOrigin: true)]
    public function renderEdit(int $id): void
    {

    }

    #[Requires(sameOrigin: true)]
    public function renderDelete(int $id): void
    {
        $res = $this->of->remove($id);
        if ($res > 0) {
            $this->flashMessage('Offer was removed', 'success');
        } else {
            $this->flashMessage('Offer was not removed', 'error');
        }

        $this->redirect('default');
    }
}
