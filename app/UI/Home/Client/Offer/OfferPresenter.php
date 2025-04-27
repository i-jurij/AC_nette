<?php

declare(strict_types=1);

namespace App\UI\Home\Client\Offer;

use App\Model\OfferFacade;
use App\UI\Accessory\IsBot;
use Nette\Application\UI\Form;
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
        if ($this->getUser()->isLoggedIn()) {
            $formdata = new \stdClass();
            $formdata->client_id = $this->getUser()->getId();

            $offersCount = $this->of->offersCount(location: $this->locality, form_data: $formdata);
            $paginator = new Paginator();
            $paginator->setItemCount($offersCount);
            $paginator->setItemsPerPage($this->items_on_page_paginator);
            $paginator->setPage($page);
            $this->template->paginator = $paginator;

            $this->template->offers = $this->of->getOffers(form_data: $formdata);

        // $this->template->backlink = $this->storeRequest();
        } else {
            $this->error();
        }
    }

    public function actionAdd()
    {
        if ($this->getUser()->isLoggedIn()) {
            $form = $this->getComponent('offerForm');
            $form->onSuccess[] = [$this, 'addingOfferFormSucceeded'];
        } else {
            $this->error();
        }
    }

    public function actionEdit(int $o, int $c)
    {
        if ($this->getUser()->isLoggedIn() && $this->getUser()->getId() == $c) {
            $fd = new \stdClass();
            $fd->id = $o;
            $fd->client_id = $c;
            $offers = $this->of->getOffers(form_data: $fd);

            $form = $this->getComponent('offerForm');
            $form->setDefaults($offers[0]); // установка значений по умолчанию
            $form->onSuccess[] = [$this, 'editingOfferFormSucceeded'];
        } else {
            $this->error();
        }
    }

    protected function createComponentOfferForm(): Form
    {
        // проверяем, что действие - 'add' или 'edit'
        if (!in_array($this->getAction(), ['add', 'edit'])) {
            $this->error();
        }

        $form = new Form();

        // ... добавляем поля формы ...

        return $form;
    }

    public function addingOfferFormSucceeded(Form $form, array $data): void
    {
        $this->of->add($data); // добавление записи в базу данных
        $this->flashMessage('Успешно добавлено');
        $this->redirect('...');
    }

    public function editingOfferFormSucceeded(Form $form, array $data): void
    {
        $id = (int) $this->getParameter('id');
        $this->of->update($id, $data); // обновление записи
        $this->flashMessage('Успешно обновлено');
        $this->redirect('...');
    }

    public function handleRemove(int $o, int $c)
    {
        if ($this->getUser()->isLoggedIn() && $this->getUser()->getId() == $c) {
            $this->of->remove($o);
        }
        $this->redirect('this');
    }
}

class OfferTemplate extends \App\UI\Home\BaseTemplate
{
    public array $offers;
    public Paginator $paginator;
    public string $backlink;
}
