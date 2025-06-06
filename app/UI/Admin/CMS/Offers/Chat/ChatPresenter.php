<?php

declare(strict_types=1);

namespace App\UI\Admin\CMS\Offers\Chat;


use \App\Model\ChatFacade;
use \Nette\Utils\ArrayHash;
final class ChatPresenter extends \App\UI\Admin\BasePresenter
{
    public function __construct(
        protected ChatFacade $chat
    ) {
        parent::__construct();
    }

    public function renderDefault()
    {
        $this->redirectPermanent(':Admin:CMS:Offers:');
    }

    #[Requires(sameOrigin: true)]
    public function renderDetail($offer_id, $offer_owner_id)
    {
        $oi = (int) strip_tags($offer_id);
        $ooi = (int) strip_tags($offer_owner_id);
        $m = $this->chat->AdmGetByOffer($oi) ?? [];
        $message = [];
        foreach ($m as $row) {
            $message[$row->client_id_who][] = \get_object_vars($row);
        }

        if (isset($message[$ooi])) {
            foreach ($message[$ooi] as $key => $value) {
                if (isset($message[$value['client_id_to_whom']])) {
                    $message[$value['client_id_to_whom']][] = $value;
                    unset($message[$ooi][$key]);
                }
            }
            unset($message[$ooi]);
        }
        foreach ($message as $k => $val) {
            uasort($message[$k], function ($a, $b) {
                if (strtotime($a['created_at']->format('Y-m-d H:i:s')) == \strtotime($b['created_at']->format('Y-m-d H:i:s'))) {
                    return 0;
                }
                return \strtotime($a['created_at']->format('Y-m-d H:i:s')) < \strtotime($b['created_at']->format('Y-m-d H:i:s')) ? -1 : 1;
            });
        }
        $this->template->chat = $message;
        $this->template->offer_id = $oi;
        $this->template->offer_owner_id = $ooi;
    }

    #[Requires(sameOrigin: true)]
    public function handleDelete($id): void
    {
        if (!$this->getUser()->isAllowed('Chat', 'delete')) {
            $this->error('Forbidden', 403);
        }
        $res = $this->chat->delete((int) $id);
        if ($res === 1) {
            $this->flashMessage('Deleted', 'success');
        } else {
            $this->flashMessage('Not deleted. Something wrong...', 'error');
        }

        $this->redirect('this');
    }

    #[Requires(sameOrigin: true)]
    public function actionDelete($offer_id, $client_id = null): void
    {
        if (!$this->getUser()->isAllowed('Chat', 'deleteByOffer')) {
            $this->error('Forbidden', 403);
        }
        if (!empty((int) $client_id)) {
            $res = $this->chat->deleteByOfferClient((int) $offer_id, (int) $client_id);
        } else {
            $res = $this->chat->deleteByOffer((int) $offer_id);
        }

        if ($res !== null) {
            $this->flashMessage('Deleted', 'success');
        } else {
            $this->flashMessage('Not deleted. Something wrong...', 'error');
        }

        $this->redirect(':Admin:CMS:Offers:');
    }

}