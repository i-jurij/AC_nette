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

            if (Csrf::isValid() && Csrf::isRecent()) {
                $d = $this->preparePostData($this->post_data);
                if (!empty($this->post_data['firstGetChat']) && $this->post_data['firstGetChat'] === 'true') {
                    $message = $this->get();
                    $output = $this->getHtml($message);
                }

                if (!empty($this->post_data['update_chat']) && $this->post_data['update_chat'] === 'true') {
                    //update chats messages
                    //$output = $this->chatFacade->countChat(client_id: $this->getUser()->getId());

                    //// TEST
                    $output = 'message list';
                    //// END TEST
                }

                if (!empty($this->post_data['createMessage_chat']) && $this->post_data['createMessage_chat'] === 'true') {
                    $output = $this->save();
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

        if (!empty($message)) {
            $client_id = $this->user->getId();
            foreach ($message as $row) {
                if ($row['client_id_to_whom'] == $client_id) {
                    $ids[] = $row['id'];
                }
            }
            if (!empty($ids)) {
                $this->chatFacade->markRead($ids);
            }
        }

        return $message;
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

    private function getHtml(array $messages): string
    {
        $m = [];
        foreach ($messages as $message) {
            $m[$message['client_id_who']]['name'] = $message['username'];
            $m[$message['client_id_who']]['message'][] = $message;
        }

        $current_user_id = $this->user->getId();

        if (!empty($m[$current_user_id]) && !empty($m[$current_user_id]['message'])) {
            foreach ($m[$current_user_id]['message'] as $k => $mes) {
                if (!empty($m[$mes['client_id_to_whom']])) {
                    $m[$mes['client_id_to_whom']]['message'][] = $mes;
                    unset($m[$current_user_id]['message'][$k]);
                }
            }
            unset($m[$current_user_id]);
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
            'user' => $this->template->user
        ];
        $template = APPDIR . DIRECTORY_SEPARATOR . 'UI' . DIRECTORY_SEPARATOR . 'shared_templates' . DIRECTORY_SEPARATOR . 'chat.latte';
        $output = $latte->renderToString($template, $params);
        return $output;
    }
}
