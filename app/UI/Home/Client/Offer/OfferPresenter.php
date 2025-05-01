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
use Nette\Http\FileUpload;
use App\UI\Accessory\Moderating\ModeratingText;

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
        /*
                $form->addGroup('');
                $form->addMultiUpload('photos', 'Фото: (до 4-х штук)')
                    ->addRule($form::MaxLength, '%d фото максимум.', 4)
                    ->addRule($form::Image, 'Avatar must be JPEG, PNG, WebP.')
                    ->addRule($form::MaxFileSize, 'Maximum size is 1 MB.', 1024 * 1024);
        */
        $form->addGroup('');
        $form->addUpload('photo1', 'Фото 1')
            ->addRule($form::Image, 'Avatar must be JPEG, PNG, WebP.')
            ->addRule($form::MaxFileSize, 'Maximum size is 10 MB.', 1024 * 1024 * 10);

        $form->addUpload('photo2', 'Фото 2')
            ->addRule($form::Image, 'Avatar must be JPEG, PNG, WebP.')
            ->addRule($form::MaxFileSize, 'Maximum size is 10 MB.', 1024 * 1024 * 10);

        $form->addUpload('photo3', 'Фото 3')
            ->addRule($form::Image, 'Avatar must be JPEG, PNG, WebP.')
            ->addRule($form::MaxFileSize, 'Maximum size is 10 MB.', 1024 * 1024 * 10);

        $form->addUpload('photo4', 'Фото 4')
            ->addRule($form::Image, 'Avatar must be JPEG, PNG, WebP.')
            ->addRule($form::MaxFileSize, 'Maximum size is 10 MB.', 1024 * 1024 * 10);

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
        $form_data = new \stdClass;
        $form_data->phone = PhoneNumber::toDb($data['phone']);
        $form_data->city = $this->locality['city'] ?: false;
        $form_data->category = (int) $form->getHttpData($form::DataText, 'category');
        $service_array = $form->getHttpData($form::DataText, 'service[]');
        foreach ($service_array as $key => $service_id) {
            $service_array[$key] = (int) $service_id;
        }
        $form_data->service = $service_array;
        $form_data->offers_type = (in_array($data['offers_type'], ['workoffer', 'serviceoffer'])) ? $data['offers_type'] : false;
        $form_data->price = (int) $data['price'];
        $form_data->client_id = (int) $data['client_id'];

        $text = htmlspecialchars(strip_tags($data['message']));
        $text = trim(mb_substr($text, 0, 500));
        $isBad = ModeratingText::isTextBad($text);
        if ($isBad === false) {
            $form_data->message = $text;
            $form_data->moderated = 1;
        }

        $form_data->request_data = \serialize($_SERVER);


        $this->of->add($data); // добавление записи в базу данных


        $file = $this->getHttpRequest()->getFile('photo1');
        if ($file instanceof FileUpload && $file?->hasFile()) {
            $file->move("/images/offers/{$form_data->client_id} ");

        }
        $this->flashMessage('Объявление успешно добавлено', 'success');
        $this->redirect(':Home:Client:Offer:default');
    }

    public function editingOfferFormSucceeded(Form $form, array $data): void
    {
        $id = (int) $this->getParameter('id');
        $this->of->update($id, $data); // обновление записи
        $this->flashMessage('Объявление успешно обновлено', 'success');
        $this->redirect(':Home:Client:Offer:default');
    }

    public function handleRemove(int $o, int $c)
    {
        if ($this->getUser()->isLoggedIn() && $this->getUser()->getId() == $c) {
            $this->of->remove($o);
        }
        $this->redirect('this');
    }

    public function actionSaveToBackend()
    {
        $city = $this->locality['city'] ?: '';
        $this->sendJson($city);
    }
}

class OfferTemplate extends \App\UI\Home\BaseTemplate
{
    public array $offers;
    public array $services;
    public Paginator $paginator;
    public string $backlink;
}
