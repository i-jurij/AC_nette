<?php

declare(strict_types=1);

namespace App\UI\Home\Client\Chat;

use \App\Model\ChatFacade;
use \Ijurij\Geolocation\Lib\Csrf;
use App\UI\Accessory\Moderating\ModeratingText;
use \Nette\Utils\ArrayHash;

class ChatPresenter extends \Nette\Application\UI\Presenter
{
    private array $post_data;

    public function __construct(
        private ChatFacade $chatFacade
    ) {
    }

    public function actionDefault($data)
    {
        if ($this->user->isLoggedIn()) {
            $httpRequest = $this->getHttpRequest();
            $this->post_data = $httpRequest->getPost();

            if (Csrf::isValid()) {
                if (!empty($this->post_data['firstGetChat']) && $this->post_data['firstGetChat'] === 'true') {
                    $message = $this->get();
                    $offer_owner_id = (int) $this->post_data['offer_owner_id'];
                    $offer_id = (int) $this->post_data['offer_id'];
                    $output = $this->getHtml($message, $offer_owner_id, $offer_id);
                }

                if (!empty($this->post_data['update_chat']) && $this->post_data['update_chat'] === 'true') {
                    $d = $this->preparePostData($this->post_data);
                    $messages = $this->chatFacade->getByOfferNoRead(data: $d);
                    $output = [];
                    foreach ($messages as $message) {
                        $output[$message['client_id_who']]['name'] = $message['username'];
                        $output[$message['client_id_who']]['message'][] = $message;
                    }
                    $res_mark_read = $this->markRead($messages);
                }

                if (!empty($this->post_data['createMessage_chat']) && $this->post_data['createMessage_chat'] === 'true') {
                    $output = $this->save();
                }

                if (!empty($this->post_data['updNoReadCount_chat']) && $this->post_data['updNoReadCount_chat'] === 'true') {
                    $d = $this->preparePostData($this->post_data);
                    if ($d->client_id_to_whom == $this->getUser()->getId()) {
                        $allNoRead = $this->chatFacade->countChat(client_id: $d->client_id_to_whom);
                        $output['allNoRead'] = $allNoRead;
                        if (!empty($d->offer_id)) {
                            $offerNoRead = $this->chatFacade->countChatOffer(client_id: $this->getUser()->getId(), offer_id: $d->offer_id);
                            $output['offerNoRead'] = $offerNoRead;
                        }
                    } else {
                        $output = false;
                    }
                }

                $this->sendJson($output);
            } else {
                $this->sendJson(false);
            }
        } else {
            $this->sendJson(false);
        }
    }
    public function get()
    {
        $message = [];
        $d = $this->preparePostData($this->post_data);

        if (!empty($d->offer_id)) {
            $message = $this->chatFacade->getByOffer(data: $d);
        } else {
            $message = $this->chatFacade->getByClient(data: $d);
        }

        $res_mark_read = $this->markRead($message);

        return $message;
    }

    private function markRead(array $message): int|null
    {
        if (!empty($message)) {
            $client_id = $this->user->getId();
            foreach ($message as $row) {
                if ($row['client_id_to_whom'] == $client_id) {
                    $ids[] = $row['id'];
                }
            }
            if (!empty($ids)) {
                return $this->chatFacade->markRead($ids);
            }
        }
        return null;
    }

    public function save(): array
    {
        $d = $this->preparePostData($this->post_data);

        if (
            !empty($d->message) && !empty($d->client_id_who)
            && !empty($d->client_id_to_whom) && $d->client_id_who !== $d->client_id_to_whom
            && !empty($d->offer_id)
        ) {
            return $this->chatFacade->create($d);
        }
        return [];
    }

    public function delete()
    {

    }

    private function preparePostData(array $data): ArrayHash
    {
        $d = new ArrayHash();
        if (!empty($data['offer_id'])) {
            $d->offer_id = (int) htmlspecialchars(strip_tags($data['offer_id']));
        }
        /*
        if (!empty($data['offer_owner_id'])) {
            $d->offer_owner_id = (int) htmlspecialchars(strip_tags($data['offer_owner_id']));
        }
        */
        if (!empty($data['client_id_who'])) {
            $d->client_id_who = (int) htmlspecialchars(strip_tags($data['client_id_who']));
        }
        if (!empty($data['client_id_to_whom'])) {
            $d->client_id_to_whom = (int) htmlspecialchars(strip_tags($data['client_id_to_whom']));
        }
        /*
        if (!empty($data['parent_id'])) {
            $d->parent_id = (int) htmlspecialchars(strip_tags($data['parent_id']));
        }
        */
        $d->request_data = \serialize($_SERVER);
        // moderate comment_text here or into other method
        if (!empty($data['message'])) {
            $text = htmlspecialchars(strip_tags($data['message']));
            $text = trim(mb_substr($text, 0, 500));
            $isBad = ModeratingText::isTextBad($text);

            if ($isBad === false) {
                $d->message = $text;
                $d->moderated = 1;
            }
        }
        return $d;
    }

    private function getHtml(array $messages, int $offer_owner_id, int $offer_id): string
    {
        $m = [];
        foreach ($messages as $message) {
            $m[$message['client_id_who']]['name'] = $message['username'];
            $m[$message['client_id_who']]['message'][] = $message;
        }

        $current_user_id = $this->user->getId();

        if ($current_user_id == $offer_owner_id) {
            $cid = $current_user_id;
        } else {
            $cid = $offer_owner_id;
        }

        if (!empty($m[$cid]) && !empty($m[$cid]['message'])) {
            foreach ($m[$cid]['message'] as $k => $mes) {
                if (!empty($m[$mes['client_id_to_whom']])) {
                    $m[$mes['client_id_to_whom']]['message'][] = $mes;
                    unset($m[$cid]['message'][$k]);
                }
            }
            unset($m[$cid]);
        }

        if (!empty($m)) {
            foreach ($m as $km => $value) {
                uasort($m[$km]['message'], function ($a, $b) {
                    if (strtotime($a['created_at']->format('Y-m-d H:i:s')) == \strtotime($b['created_at']->format('Y-m-d H:i:s'))) {
                        return 0;
                    }
                    return \strtotime($a['created_at']->format('Y-m-d H:i:s')) < \strtotime($b['created_at']->format('Y-m-d H:i:s')) ? -1 : 1;
                });
            }
        }

        $latte = $this->template->getLatte();
        $params = [
            'message' => $m,
            'user' => $this->template->user,
            'offer_owner_id' => $offer_owner_id,
            'offer_id' => $offer_id
        ];
        $template = APPDIR . DIRECTORY_SEPARATOR . 'UI' . DIRECTORY_SEPARATOR . 'shared_templates' . DIRECTORY_SEPARATOR . 'chat.latte';
        $output = $latte->renderToString($template, $params);
        return $output;
    }
}
