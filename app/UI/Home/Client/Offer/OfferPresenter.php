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
use \Nette\Utils\ArrayHash;
use Nette\Utils\Image;
use Nette\Utils\ImageColor;
use Nette\Utils\ImageType;
use Nette\Utils\FileSystem;
use Nette\Utils\Finder;



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

            $this->template->offers = $offers[0];


            //$this->template->images = Finder::findFiles([(string) $o . '_*.jpg', (string) $o . '_*.jpeg', (string) $o . '_*.png', (string) $o . '_*.webp'])
            //  ->in(WWWDIR . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "offers");

            $this->template->images = Finder::findFiles([(string) $o . '_*.jpg', (string) $o . '_*.jpeg', (string) $o . '_*.png', (string) $o . '_*.webp'])
                ->in(WWWDIR . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "offers")->collect();

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

        /*
        $form->addHidden('city_name')->setOmitted();
        $form->addHidden('region_name')->setOmitted();
        */

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
            ->addRule($form::Image, 'Тип фото JPEG, PNG, WebP.')
            ->addRule($form::MaxFileSize, 'Maximum size is 10 MB.', 1024 * 1024 * 10);

        $form->addUpload('photo2', 'Фото 2')
            ->addRule($form::Image, 'Тип фото JPEG, PNG, WebP.')
            ->addRule($form::MaxFileSize, 'Maximum size is 10 MB.', 1024 * 1024 * 10);

        $form->addUpload('photo3', 'Фото 3')
            ->addRule($form::Image, 'Тип фото JPEG, PNG, WebP.')
            ->addRule($form::MaxFileSize, 'Maximum size is 10 MB.', 1024 * 1024 * 10);

        $form->addUpload('photo4', 'Фото 4')
            ->addRule($form::Image, 'Тип фото JPEG, PNG, WebP.')
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
        $form->addHidden('id');

        $form->addSubmit('offer_add', 'Сохранить');

        return $form;
    }

    protected function prepareOfferFormData(Form $form, array $data): ArrayHash
    {
        $form_data = new ArrayHash;

        if (!empty($data['id'])) {
            $form_data->id = (int) $data['id'];
        }

        $form_data->client_id = (int) $data['client_id'];

        $form_data->city_name = $this->locality['city'] ?: false;
        $form_data->region_name = $this->locality['region'] ?: false;

        $form_data->offers_type = (in_array($data['offers_type'], ['workoffer', 'serviceoffer'])) ? $data['offers_type'] : false;
        $form_data->price = (int) $data['price'];
        $form_data->moderated = 0;
        $text = htmlspecialchars(strip_tags($data['message']));
        $text = trim(mb_substr($text, 0, 500));
        $isBad = ModeratingText::isTextBad($text);
        if ($isBad === false) {
            $form_data->message = $text;
            $form_data->moderated = 1;
        }

        $form_data->request_data = \serialize($_SERVER);

        return $form_data;
    }

    protected function addNewOffer($form, $data)
    {
        $form_data = $this->prepareOfferFormData($form, $data);

        if (!empty($form_data) && $form_data->moderated === 1) {
            $new_offer_id = $this->of->add($form_data);

            if (!empty($new_offer_id)) {
                $this->flashMessage('Объявление успешно добавлено', 'success');
                // update clients phone 
                $phone = PhoneNumber::toDb($data['phone']);
                $client_phone = $this->sf->db->table('client')
                    ->where('id', $form_data->client_id)
                    ->update([
                        'phone' => $phone
                    ]);
                if ($client_phone > 0) {
                    $this->flashMessage('Номер телефона обновлен', 'success');
                }

                // add offers services 
                // $category_id = (int) $form->getHttpData($form::DataText, 'category');
                $service_array = $form->getHttpData($form::DataText, 'service[]');
                foreach ($service_array as $service_id) {
                    $services[] = [
                        'offer_id' => $new_offer_id,
                        'service_id' => (int) $service_id
                    ];
                }
                /* $offer_service = $this->sf->db->table('offer_service')->insert($services);
                if (!empty($offer_service)) {
                    $this->flashMessage('Услуги успешно добавлены', 'success');
                }
                */
                $this->of->db->query("INSERT INTO `offer_service` ?", $services);
                $offer_service_res = $this->of->db->getInsertId();

                if (!empty($offer_service_res)) {
                    $this->flashMessage('Услуги успешно добавлены', 'success');
                }

                $this->imagesAdd($new_offer_id);
            } else {
                $this->flashMessage('Объявление не добавлено. Попробуйте позже.', 'error');
            }
        } else {
            $this->flashMessage('Объявление не добавлено. В сообщении не должно быть ссылок или ругательств', 'success');
        }
    }

    protected function imagesAdd(string $new_offer_id)
    {
        //add offers images
        $files_array = [
            '1' => $this->getHttpRequest()->getFile('photo1'),
            '2' => $this->getHttpRequest()->getFile('photo2'),
            '3' => $this->getHttpRequest()->getFile('photo3'),
            '4' => $this->getHttpRequest()->getFile('photo4'),
        ];
        $path = WWWDIR . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR;
        // $logo = Image::fromFile(file: $path . 'logo.png');
        foreach ($files_array as $key => $file) {
            if (
                $file instanceof FileUpload
                && $file?->hasFile()
                && $file?->isOk()
                && $file?->isImage()
                && $file?->getSize() < 10 * 1024 * 1024
                && in_array($file?->getSuggestedExtension(), ['jpg', 'jpeg', 'png', 'webp'])
            ) {

                // $file?->getImageSize() = [0 => width, 1 => height]
                $size = $file?->getImageSize();
                $width = (!empty($size[0])) ? $size[0] : null;
                $height = (!empty($size[1])) ? $size[1] : null;

                if ($width > 2048 || $height > 2048) {
                    $image = $file->toImage();
                    //$image->resize(width: 2048, height: 2048, mode: Image::ShrinkOnly);
                    $image->resize(width: 2048, height: 2048);
                    // $image->sharpen();
                    //$image->place(image: $logo, left: '80%', top: '80%', opacity: 25); //watermark
                    $image->save(file: $path . "offers" . DIRECTORY_SEPARATOR . $new_offer_id . '_' . $key . '.png', quality: null, type: ImageType::PNG);
                } else {
                    $file->move(dest: $path . "offers" . DIRECTORY_SEPARATOR . $new_offer_id . '_' . $key . '.' . $file->getSuggestedExtension());
                }

                $this->flashMessage("Фото $key успешно добавлено", "success");

                if ($key == '1') {
                    $thumb = $file->toImage()->resize(1024, 960, Image::Cover)->sharpen();
                    // $thumb->place(image: $logo, left: '80%', top: '80%', opacity: 25); //watermark
                    $offer_thumb = $this->sf->db
                        ->table('offer_image_thumb')
                        ->insert([
                            'offer_id' => $new_offer_id,
                            'caption' => '',
                            'thumb' => $thumb
                        ]);
                    if (!empty($offer_thumb)) {
                        $this->flashMessage('Изабражение для объявления успешно добавлено в базу данных', 'success');
                    }
                }
            }
        }
        /*
        foreach ($files_array as $value) {
            if ($value) {
                $thumb = $value->toImage()->resize(1024, 960, Image::Cover)->sharpen();
                // $thumb->place(image: $logo, left: '80%', top: '80%', opacity: 25); //watermark

                $offer_thumb = $this->sf->db
                    ->table('offer_image_thumb')
                    ->insert([
                        'offer_id' => $new_offer_id,
                        'caption' => '',
                        'thumb' => $thumb
                    ]);
                if (!empty($offer_thumb)) {
                    $this->flashMessage('Изабражение для объявления успешно добавлено в базу данных', 'success');
                }
                return;
            }
        }
        */
    }

    public function handleImageDel(string $image_name)
    {
        $filename = WWWDIR . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "offers" . DIRECTORY_SEPARATOR . \basename($image_name);
        if (\file_exists($filename)) {
            FileSystem::delete($filename);
        }
        $this->redirect('this');
    }
    public function addingOfferFormSucceeded(Form $form, array $data): void
    {
        $this->addNewOffer($form, $data);
        $this->redirect(':Home:Client:Offer:default');
    }

    public function editingOfferFormSucceeded(Form $form, array $data): void
    {
        $id = (int) $this->getParameter('o');
        $form_data = $this->prepareOfferFormData($form, $data);

        if ($id == $form_data->id) {
            // update clients phone 
            $phone = PhoneNumber::toDb($data['phone']);
            $client_phone = $this->sf->db->table('client')
                ->where('id', $form_data->client_id)
                ->update([
                    'phone' => $phone
                ]);
            if ($client_phone > 0) {
                $this->flashMessage('Номер телефона обновлен', 'success');
            }

            // add offers services 
            // $category_id = (int) $form->getHttpData($form::DataText, 'category');
            $service_array = $form->getHttpData($form::DataText, 'service[]');
            foreach ($service_array as $service_id) {
                $services[] = [
                    'offer_id' => $form_data->id,
                    'service_id' => (int) $service_id
                ];
            }
            $this->of->db->query("DELETE FROM `offer_service` WHERE offer_id = ?", $form_data->id);
            $this->of->db->query("INSERT IGNORE INTO `offer_service` ?", $services);
            $offer_service_res = $this->of->db->getInsertId();

            if (!empty($offer_service_res)) {
                $this->flashMessage('Услуги успешно обновлены', 'success');
            }

            $this->imagesAdd((string) $form_data->id);

            $this->of->update($id, $form_data);

            $this->flashMessage('Объявление успешно обновлено', 'success');
        } else {
            $this->flashMessage('Объявление не обновлено', 'warning');
        }

        $this->redirect(':Home:Client:Offer:default');
    }

    public function handleRemove(int $o, int $c)
    {
        if ($this->getUser()->isLoggedIn() && $this->getUser()->getId() == $c) {
            if ($this->of->remove($o) > 0) {
                $this->flashMessage('Объявление удалено из базы данных', 'success');
            }

            $images = Finder::findFiles([(string) $o . '_*.jpg', (string) $o . '_*.jpeg', (string) $o . '_*.png', (string) $o . '_*.webp'])
                ->in(WWWDIR . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "offers");
            foreach ($images as $name => $file) {
                FileSystem::delete($name);
            }
            $this->flashMessage('Фото для объявления удалены', 'success');
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
    // public Finder $images;
    public array $images;
    public Paginator $paginator;
    public string $backlink;
}
