<?php

declare(strict_types=1);

namespace App\UI\Home\Offer;

use \App\Model\ChatFacade;
use App\Model\CommentFacade;
use App\Model\OfferFacade;
use App\Model\RatingFacade;
use App\Model\GrievanceFacade;
use App\UI\Accessory\FormFactory;
use App\UI\Accessory\Ip;
use App\UI\Accessory\Moderating\ModeratingText;
use Nette\Application\UI\Form;
use \Ijurij\Geolocation\Lib\Csrf;
use Nette\Utils\Validators;


/**
 * @property OfferTemplate $template
 */
final class OfferPresenter extends \App\UI\Home\BasePresenter
{
    public function __construct(
        private OfferFacade $offers,
        private FormFactory $formFactory,
        private RatingFacade $rf,
        private CommentFacade $cf,
        private GrievanceFacade $gr,
        protected ChatFacade $chat,
    ) {
        parent::__construct($this->chat);
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
            $this->template->comments_count = $this->offers->db->query('SELECT count(*) FROM `comment` WHERE `offer_id` = ? AND `moderated` = 1', $id)->fetchField();
            if ($this->getUser()->isLoggedIn()) {
                $this->template->count_offer_chat = $this->chat->countChatOffer(client_id: $this->getUser()->getId(), offer_id: $id);
            } else {
                $this->template->count_offer_chat = 0;
            }
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

    public function actionGetComment()
    {
        if ($this->getUser()->isLoggedIn()) {
            $comments = [];
            $httpRequest = $this->getHttpRequest();
            $offer_id = (int) htmlspecialchars($httpRequest->getPost('offer_id'));
            $column = "`comment`.`id`, parent_id, client_id, comment_text, DATE_FORMAT(`comment`.created_at, '%e.%m.%Y %H:%i') AS created_at";
            $client_column = '`username`, `image`, `phone`, `phone_verified`, `email`, `email_verified`, `rating`';
            $inner_join = 'INNER JOIN `client` ON `comment`.`client_id` = `client`.`id`';
            $where_offer = '`moderated` = 1 AND `offer_id` = ?';
            $sql = "SELECT $column, $client_column FROM `comment` $inner_join WHERE $where_offer ORDER BY `comment`.created_at DESC";

            $comments = $this->offers->db->query($sql, $offer_id)->fetchAll();

            $this->sendJson($comments);
            exit;
        }
        $this->sendJson([]);
        exit;
    }

    public function createComponentOfferCommentForm()
    {
        $form = new Form();
        $form->addProtection('Csrf error');
        // $form->setTranslator($this->translator);
        $renderer = $form->getRenderer();
        $renderer->wrappers['group']['container'] = 'div class="my1 mx-auto pb2 px2"';
        $renderer->wrappers['controls']['container'] = 'div';
        $renderer->wrappers['pair']['container'] = 'div';
        $renderer->wrappers['label']['container'] = null;
        $renderer->wrappers['control']['container'] = null;
        $form->setHtmlAttribute('id', 'offer_comment_form');

        $form->addTextArea('comment_text')
            ->setHtmlAttribute('rows', '4')
            ->setHtmlAttribute('cols', '100')
            ->setHtmlAttribute('class', 'offer_comment_form_input')
            ->addCondition($form::Length, [8, 500])
            // ->addRule($form::Pattern, 'Только буквы, цифры и знаки препинания', '^[а-яА-Яa-zA-Z0-9\s?!,.\'Ёё]+$')
            ->addRule($form::Pattern, 'Только буквы, цифры и знаки препинания', '^[\p{L}\d ?!,.-_~\":;!]+$')
            ->setRequired();

        $form->addHidden('offer_id')->setHtmlAttribute('class', 'offer_comment_form_input')->setRequired();
        $form->addHidden('client_id')->setHtmlAttribute('class', 'offer_comment_form_input')->setRequired();
        $form->addHidden('parent_id')->setHtmlAttribute('class', 'offer_comment_form_input')->setRequired();

        // $form->addSubmit('offer_comment_form_submit', 'Комментировать');
        $form->onSuccess[] = [$this, 'actionOfferCommentAdd'];

        return $form;
    }

    #[Requires(methods: 'POST', sameOrigin: true)]
    public function actionOfferCommentAdd()
    {
        if ($this->user->isLoggedIn()) {
            $httpRequest = $this->getHttpRequest();
            $data = $httpRequest->getPost();

            $d = new \Nette\Utils\ArrayHash();
            $d->offer_id = (int) htmlspecialchars(strip_tags($data['offer_id']));
            $d->client_id = (int) htmlspecialchars(strip_tags($data['client_id']));
            if (!empty($data['parent_id'])) {
                $d->parent_id = (int) htmlspecialchars(strip_tags($data['parent_id']));
            }
            $d->request_data = \serialize($_SERVER);
            // moderate comment_text here or into other method
            if ($data['comment_text']) {
                $text = htmlspecialchars(strip_tags($data['comment_text']));
                $text = trim(mb_substr($text, 0, 500));
                $isBad = ModeratingText::isTextBad($text);

                if ($isBad === false) {
                    $d->comment_text = $text;
                    $d->moderated = 1;
                }
                // $d->comment_text = ModeratingText::cleanText($text);
                // $d->moderated = 1;
            }

            if (!empty($d->comment_text)) {
                $res = $this->cf->create($d);
                if (!empty($res)) {
                    $this->sendJson(true);
                } else {
                    $this->sendJson(false);
                }
            } else {
                $this->sendJson(false);
            }
        } else {
            $this->sendJson(false);
        }
    }

    #[Requires(methods: 'POST', sameOrigin: true)]
    public function actionSimilar()
    {
        if (Csrf::isValid() && Csrf::isRecent()) {
            $httpRequest = $this->getHttpRequest();
            $not_id = htmlspecialchars(strip_tags($httpRequest->getPost('not_id')));
            $type = $httpRequest->getPost('type');
            $services = unserialize($httpRequest->getPost('services'));
            $price = (float) htmlspecialchars(strip_tags($httpRequest->getPost('price')));

            $form_data = new \stdClass;
            $form_data->not_id = (!empty($not_id)) ? $not_id : null;
            $form_data->offertype = (!empty($type) && in_array($type, ['serviceoffer', 'workoffer'])) ? $type : '';

            $service = [];
            if (Validators::is($services, 'array')) {
                foreach ($services as $category) {
                    if (
                        Validators::is($category['category_id'], 'int') && Validators::is($category['services'], 'array')
                    ) {
                        foreach ($category['services'] as $serv) {
                            if (Validators::is($serv['id'], 'int')) {
                                $service[] = $serv['id'];
                            }
                        }
                    }
                }
            }
            $form_data->service = \serialize($service);

            $form_data->price_min = (($price - $price / 2) > 0) ? ($price - $price / 2) : $price;
            $form_data->price_max = $price + $price / 2;

            $limit = 20;
            $page = (!empty($httpRequest->getPost('page'))) ? (int) htmlspecialchars(strip_tags($httpRequest->getPost('page'))) : false;
            $page = (empty($page)) ? 1 : $page;
            $offset = ($page != 1) ? $page * $limit - $limit : 0;

            $similar = $this->offers->getSimilar(location: $this->locality, limit: $limit, offset: $offset, form_data: $form_data);
            $countSimilar = $this->offers->offersCount(location: $this->locality, form_data: $form_data);

            $latte = $this->template->getLatte();
            $params = [
                'offers' => $similar,
                'user' => $this->user,
                'baseUrl' => $this->template->baseUrl,
                'page' => $page,
                'maxPage' => ceil($countSimilar / $limit)
            ];

            $template = APPDIR . DIRECTORY_SEPARATOR . 'UI' . DIRECTORY_SEPARATOR . 'shared_templates' . DIRECTORY_SEPARATOR . 'offers_list.latte';
            $output = $latte->renderToString($template, $params);

            $this->sendJson($output);
        } else {
            $this->sendJson(false);
        }
    }

    public function createComponentOfferGrievanceForm()
    {
        $form = new Form();
        $form->addProtection('Csrf error');
        // $form->setTranslator($this->translator);
        $renderer = $form->getRenderer();
        $form->setHtmlAttribute('id', 'offer_grievance_form');

        $form->addTextArea('message')
            ->setHtmlAttribute('rows', '4')
            ->setHtmlAttribute('cols', '100')
            ->addCondition($form::Length, [8, 500])
            ->addRule($form::Pattern, 'Только буквы, цифры и знаки препинания', '^[\p{L}\d ?!,.-_~\":;!]+$')
            ->setRequired();

        $form->addHidden('offer_id')->setRequired();
        $form->addHidden('client_id_who')->setRequired();
        $form->addHidden('comment_id')->setHtmlAttribute('id', 'offer_grievance_form_comment_id_input');
        $form->addSubmit('sendGrievance', 'Отправить');
        $form->onSuccess[] = [$this, 'actionGrievance'];

        return $form;
    }

    private function preGrivanceFormData($data): array
    {
        $d = [];
        if ($data->message) {
            $text = htmlspecialchars(strip_tags($data->message));
            $text = trim(mb_substr($text, 0, 500));
            $isBad = ModeratingText::isTextBad($text);

            if ($isBad === false) {
                $d['message'] = $text;
            }
        }
        if ($data->comment_id) {
            $d['comment_id'] = (int) $data->comment_id;
        }

        if (!empty($d['message'])) {
            $d['offer_id'] = (int) $data->offer_id;
            $d['client_id_who'] = (int) $data->client_id_who;
        }
        return $d;
    }

    #[Requires(methods: 'POST', sameOrigin: true)]
    public function actionGrievance(Form $form, $data)
    {
        $d = $this->preGrivanceFormData($data);
        if (!empty($d['message'])) {
            if ($this->gr->create($d) > 0) {
                $this->flashMessage('Отправлено', 'success');
            }
        }
        $this->redirect('this');
    }
    #[Requires(methods: 'POST', sameOrigin: true)]
    public function actionGrievanceJs($data)
    {
        if (Csrf::isValid() && Csrf::isRecent()) {
            $data = (object) $this->getHttpRequest()->getPost();
            $d = $this->preGrivanceFormData($data);
            if (!empty($d['message'])) {
                if ($this->gr->create($d) > 0) {
                    $this->sendJson('Отправлено');
                }
            }
        }
        $this->sendJson('Не отправлено');
    }
}

class OfferTemplate extends \App\UI\Home\BaseTemplate
{
    public array $offers;
    public string $backlink;
    public array $offer_images;
    public int $comments_count;
    public int $count_offer_chat;

}
