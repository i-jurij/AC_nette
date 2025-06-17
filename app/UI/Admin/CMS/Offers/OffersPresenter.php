<?php

declare(strict_types=1);

namespace App\UI\Admin\CMS\Offers;

use App\Model\OfferFacade;
use App\Model\ServiceFacade;
use \App\Model\ChatFacade;
use \App\Model\CommentFacade;
use Nette\Utils\Paginator;
use Nette\Utils\FileSystem;
use Nette\Utils\Finder;
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

    /**
     * @param string $tm - '-1 hour', '-1 day' eg
     */
    public function renderDefault(string $tm = 'one', int $p = 1)
    {
        if (!$this->getUser()->isAllowed('Offers', '')) {
            $this->error('Forbidden', 403);
        }

        $this->template->service_list = $this->sf->getAllServices();

        $formdata = new stdClass;
        $tmstr = '-1 hour';

        if ($tm === 'six') {
            $tmstr = '-6 hour';
        }
        if ($tm === 'twelve') {
            $tmstr = '-12 hour';
        }
        if ($tm === 'day') {
            $tmstr = '-1 day';
        }
        if ($tm === 'week') {
            $tmstr = '-1 week';
        }
        if ($tm === 'month') {
            $tmstr = '-1 month';
        }
        if ($tm === 'all') {
            unset($tmstr);
        }

        if (isset($tmstr)) {
            $formdata->created_time = new \DateTimeImmutable()->modify($tmstr)->format('Y-m-d H:i:s');
        }

        $formdata->with_banned = 1;
        $this->setOfferPaginator(page: $p, form_data: $formdata, tm: strip_tags($tm));
        $this->template->offers = $this->of->getOffers(limit: $this->template->paginator->getLength(), offset: $this->template->paginator->getOffset(), form_data: $formdata);

        $this->template->comments_count = $this->comment->commentsCount($this->template->offers);
        $this->template->chat_count = $this->chat->countByOffer($this->template->offers);
    }

    #[Requires(methods: 'POST', sameOrigin: true)]
    public function renderByClient(int $id): void
    {
        $formdata = new stdClass();
        $formdata->client_id = (int) $id;
        $formdata->with_banned = true;

        $this->template->offersOld = $this->of->offersCountOld();
        $this->template->offers = $this->of->getOffers(form_data: $formdata);
        $this->template->comments_count = $this->comment->commentsCount($this->template->offers);
        $this->template->chat_count = $this->chat->countByOffer($this->template->offers);
    }

    protected function setOfferPaginator(int $page, object $form_data, string $tm)
    {
        $offersCount = $this->of->offersCount(form_data: $form_data);
        $this->template->offersCount = $offersCount;
        $paginator = new Paginator();
        $paginator->setItemCount($offersCount);
        $paginator->setItemsPerPage($this->items_on_page_paginator);
        $paginator->setPage($page);

        $this->template->paginator = $paginator;
        $this->template->tm = $tm;
    }

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

    #[Requires(sameOrigin: true)]
    public function renderDetail($id): void
    {
        $formdata = new stdClass();
        $formdata->id = (int) $id;
        $formdata->with_banned = true;

        $this->template->offers = $this->of->getOffers(form_data: $formdata);
        $this->template->comments_count = $this->comment->commentsCount($this->template->offers);
    }

    #[Requires(sameOrigin: true)]
    public function handleDeleteOld()
    {
        if (!$this->getUser()->isAllowed('Offers', 'deleteOld')) {
            $this->error('Forbidden', 403);
        }

        $ids_count = $this->of->deleteOld();

        if (isset($ids_count['countDeleted']) && $ids_count['countDeleted'] > 0) {
            $this->flashMessage('Объявления удалены', 'success');
            if (!empty($ids_count['ids'])) {
                foreach ($ids_count['ids'] as $id) {
                    $images = Finder::findFiles([(string) $id . '_*.jpg', (string) $id . '_*.jpeg', (string) $id . '_*.png', (string) $id . '_*.webp'])
                        ->in(WWWDIR . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "offers");
                    foreach ($images as $name => $file) {
                        FileSystem::delete($name);
                    }
                }
                $this->flashMessage('Фото для объявлений удалены', 'success');
            }
        }

        $this->redirect('this');
    }
}
