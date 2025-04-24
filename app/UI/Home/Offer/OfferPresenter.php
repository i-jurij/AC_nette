<?php

declare(strict_types=1);

namespace App\UI\Home\Offer;

use App\Model\CommentFacade;
use App\Model\OfferFacade;
use App\Model\RatingFacade;
use App\UI\Accessory\FormFactory;
use App\UI\Accessory\Ip;
use App\UI\Accessory\ModeratingText;
use Nette\Application\UI\Form;

/**
 * @property OfferTemplate $template
 */
final class OfferPresenter extends \App\UI\Home\BasePresenter
{
    public function __construct(
        private OfferFacade $offers,
        private FormFactory $formFactory,
        private RatingFacade $rf,
        private CommentFacade $cf
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
            $this->template->comments_count = $this->offers->db->query('SELECT count(*) FROM `comment` WHERE `offer_id` = ? AND `moderated` = 1', $id)->fetchField();
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
            $isBad = ModeratingText::parse(s: $text, delta: '0', continue: "\xe2\x80\xa6", is_html: false, replace: null, charset: 'UTF-8');
            if ($isBad === false) {
                $d->comment_text = $text;
                $d->moderated = 1;
            }
        }

        if (!empty($d->comment_text)) {
            $this->cf->create($d);
            $this->sendJson(true);
        } else {
            $this->sendJson(false);
        }
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
    public int $comments_count;
}
