<?php

declare(strict_types=1);

namespace App\UI\Home\Client\Offer;

use App\Model\OfferFacade;
use App\Model\ServiceFacade;
use App\UI\Accessory\IsBot;
use Nette\Application\UI\Form;
use Nette\Utils\Paginator;
use App\UI\Accessory\Location\Location;
use Nette\Forms\Container;
use App\UI\Accessory\PhoneNumber;

/**
 * @property OfferTemplate $template
 */
final class OfferPresenter extends \App\UI\Home\BasePresenter
{
    protected int $items_on_page_paginator = 10;

    public function __construct(
        protected OfferFacade $of,
        protected ServiceFacade $sf
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

            //$offersCount = $this->of->offersCount(location: $this->locality, form_data: $formdata);
            $offersCount = $this->of->offersCount(form_data: $formdata);
            $paginator = new Paginator();
            $paginator->setItemCount($offersCount);
            $paginator->setItemsPerPage($this->items_on_page_paginator);
            $paginator->setPage($page);
            $this->template->paginator = $paginator;

            $this->template->offers = $this->of->getOffers(form_data: $formdata);

        // $this->template->backlink = $this->storeRequest();
        } else {
            $this->redirect(':Home:Sign:in', ['backlink' => $this->storeRequest()]);
        }
    }

    public function actionAdd()
    {
        if ($this->getUser()->isLoggedIn()) {
            $this->template->services = $this->sf->getAllServices();

            $form = $this->getComponent('offerForm');
            $form->onSuccess[] = [$this, 'addingOfferFormSucceeded'];
        } else {
            $this->redirect(':Home:Sign:in', ['backlink' => $this->storeRequest()]);
        }
    }

    public function actionEdit(int $o, int $c)
    {
        if ($this->getUser()->isLoggedIn() && $this->getUser()->getId() == $c) {
            $this->template->services = $this->sf->getAllServices();

            $fd = new \stdClass();
            $fd->id = $o;
            $fd->client_id = $c;
            $offers = $this->of->getOffers(form_data: $fd);

            $form = $this->getComponent('offerForm');
            $form->setDefaults($offers[0]); // установка значений по умолчанию
            $form->onSuccess[] = [$this, 'editingOfferFormSucceeded'];
        } else {
            $this->redirect(':Home:Sign:in', ['backlink' => $this->storeRequest()]);
        }
    }

    protected function createComponentOfferForm(): Form
    {
        // проверяем, что действие - 'add' или 'edit'
        if (!in_array($this->getAction(), ['add', 'edit'])) {
            $this->error();
        }

        $form = new Form();
        $form->setHtmlAttribute('id', 'offer_add_form');

        $form->addProtection('Csrf error');

        $form->addText('phone', 'Номер мобильного теефона:')
            ->addRule($form::Pattern, 'Введен неправильный номер', PhoneNumber::PHONE_REGEX)
            ->setHtmlType('tel')
            ->setHtmlAttribute('placeholder', '☎ +7 999 333 22 22') // 📱
            ->setHtmlAttribute('id', 'user_phone_input');

        $form->addGroup('');
        $form->addMultiUpload('photos', 'Фото: (до 4-х штук)')
            ->addRule($form::MaxLength, '%d фото максимум.', 4)
            ->addRule($form::Image, 'Avatar must be JPEG, PNG, WebP.')
            ->addRule($form::MaxFileSize, 'Maximum size is 1 MB.', 1024 * 1024);

        $offers_type = [
            'workoffer' => 'Предлагаю работу',
            'serviceoffer' => 'Ищу работу',
        ];
        /*
        $form->addSelect('offers_type', 'Тип:', $offers_type)
            ->setHtmlAttribute('class', 'select')
            ->setDefaultValue('serviceoffer')
            ->setRequired();
        */
        $form->addRadioList('offers_type', 'Тип:', $offers_type)
            ->setRequired("Выберите тип объявления");

        $form->addInteger('price', "Стоимость:")
            //->setHtmlAttribute('step', '100')
            ->setHtmlAttribute('min', '0')
            ->setHtmlAttribute('max', '1000000000')
            ->setRequired("Укажите стоимость");

        $form->addTextArea('message', 'Сообщение:')
            ->setHtmlAttribute('rows', '4')
            ->setHtmlAttribute('cols', '100')
            ->addCondition($form::Length, [8, 500])
            // ->addRule($form::Pattern, 'Только буквы, цифры и знаки препинания', '^[а-яА-Яa-zA-Z0-9\s?!,.\'Ёё]+$')
            ->addRule($form::Pattern, 'Только буквы, цифры и знаки препинания', '^[\p{L}\d ?!,.-_~\":;!]+$');

        $form->addHidden('client_id');

        $form->addSubmit('offer_add', 'Добавить');

        return $form;
    }

    public function addingOfferFormSucceeded(Form $form, array $data): void
    {
        $this->of->add($data); // добавление записи в базу данных
        $this->flashMessage('Успешно добавлено', 'success');
        $this->redirect(':Home:Client:Offer:default');
    }

    public function editingOfferFormSucceeded(Form $form, array $data): void
    {
        $id = (int) $this->getParameter('id');
        $this->of->update($id, $data); // обновление записи
        $this->flashMessage('Успешно обновлено', 'success');
        $this->redirect(':Home:Client:Offer:default');
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
    public array $services;
    public Paginator $paginator;
    public string $backlink;
}
