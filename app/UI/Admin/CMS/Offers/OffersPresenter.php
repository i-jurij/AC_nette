<?php

declare(strict_types=1);

namespace App\UI\Admin\CMS\Offers;

use App\Model\OfferFacade;
use App\Model\ServiceFacade;
use \App\Model\ChatFacade;
use \App\Model\CommentFacade;
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
        protected CommentFacade $comment,
        protected ChatFacade $chat
    ) {
        parent::__construct();
    }
    public function renderDefault(int $page = 1)
    {
        if (!$this->getUser()->isAllowed('Offers', '')) {
            $this->error('Forbidden', 403);
        }
        $this->setOfferPaginator($page);
        $this->template->service_list = $this->sf->getAllServices();
        $this->template->offers = $this->of->getOffers(limit: $this->template->paginator->getLength(), offset: $this->template->paginator->getOffset());

        $this->template->comments_count = $this->comment->commentsCount($this->template->offers);
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
        $formdata = new stdClass();
        $formdata->client_id = $id;
        $formdata->with_banned = true;

        $this->template->offers = $this->of->getOffers(form_data: $formdata);
        $this->template->comments_count = $this->comment->commentsCount($this->template->offers);
    }

    /*
    private function commentsCount(array $offers)
    {
        $offer_ids = array_column($offers, 'id');
        $cc = $this->of->db->query("SELECT offer_id, COUNT(offer_id) AS count 
                                            FROM `comment`
                                            WHERE offer_id IN ? 
                                            GROUP BY offer_id", $offer_ids);
        foreach ($cc as $value) {
            $res[$value->offer_id] = $value->count;
        }
        return $res ?? [];
    }
    */

    public function handleRemove(int $id)
    {
        if (!$this->getUser()->isAllowed('Offers', 'remove')) {
            $this->error('Forbidden', 403);
        }

        if ($this->of->remove($id) > 0) {
            $this->flashMessage('Объявление удалено из базы данных', 'success');
        }

        $images = Finder::findFiles([(string) $id . '_*.jpg', (string) $id . '_*.jpeg', (string) $id . '_*.png', (string) $id . '_*.webp'])
            ->in(WWWDIR . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "offers");
        foreach ($images as $name => $file) {
            FileSystem::delete($name);
        }
        $this->flashMessage('Фото для объявления удалены', 'success');


        $this->redirect('this');
    }

    #[Requires(sameOrigin: true)]
    public function renderPictures($id): void
    {
        $id = strip_tags($id);
        $images = Finder::findFiles([(string) $id . '_*.jpg', (string) $id . '_*.jpeg', (string) $id . '_*.png', (string) $id . '_*.webp'])
            ->in(WWWDIR . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "offers");
        foreach ($images as $value) {
            $this->template->pictures[] = $value->getBasename();
        }
    }

    #[Requires(sameOrigin: true)]
    public function handleRemovePicture(string $basename): void
    {
        try {
            FileSystem::delete(WWWDIR . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "offers" . DIRECTORY_SEPARATOR . $basename);
            $this->flashMessage('Фото удалено', 'success');
        } catch (\Nette\IOException $th) {
            $this->flashMessage('Фото не удалено' . $th, 'error');
        }

        $this->redirect('this');
    }

    #[Requires(sameOrigin: true)]
    public function renderRequest($id): void
    {
        $sql = "SELECT `request_data` FROM `offer` WHERE id=?";
        $req = $this->of->db->query($sql, (int) $id)->fetchField();
        $this->template->request = !empty($req) ? unserialize($req) : [];
    }
}
