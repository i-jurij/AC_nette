<?php

namespace App\UI\Home\Client\Chat;

use \App\Model\ChatFacade;
use \Ijurij\Geolocation\Lib\Csrf;
use App\UI\Accessory\Moderating\ModeratingText;
use \Nette\Utils\ArrayHash;

class ChatPresenter extends \Nette\Application\UI\Presenter
{
    private array $post_data;

    public function __construct(
        private ChatFacade $chatFacade,
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

                if (!empty($this->post_data['getOffersWithNewMessage']) && $this->post_data['getOffersWithNewMessage'] === 'true') {
                    $d = $this->preparePostData($this->post_data);
                    if ($d->client_id_to_whom == $this->getUser()->getId()) {
                        $chat = $this->chatFacade->getByClient($d->client_id_to_whom);
                        if (!empty($chat) && !empty($chat[0])) {
                            $of_ids = array_column($chat, 'offer_id');
                            $numberOfNewByOffer = array_count_values($of_ids); // return ['offer_id' => number, ...]
                            $offers_ids = array_unique($of_ids, SORT_NUMERIC);

                            $sql_offers = "SELECT
                                            `offer`.`id`,
                                            `offer`.`offers_type`,
                                            `offer`.`city_id` ,
                                            `offer`.`city_name`,
                                            `offer`.`region_id`,
                                            `offer`.`region_name`,
                                            `offer`.`price`, 
                                            `offer`.`message`,
                                            `client`.`id` AS client_id,
                                            `client`.`username` AS client_name
                                        FROM offer 
                                        INNER JOIN `client` ON offer.client_id = `client`.id
                                        WHERE `offer`.`id` IN ?";

                            $offers = $this->chatFacade->db->query($sql_offers, $offers_ids);

                            $sql_images = 'SELECT * FROM `offer_image_thumb` WHERE';
                            $offer_images = $this->chatFacade->db->query($sql_images, ['offer_id' => $offers_ids])->fetchAll();

                            $sql_services = 'SELECT 
                                `offer_service`.`offer_id`,
                                `service`.`id`,
                                `service`.`name`,
                                `category`.`id` AS category_id,
                                `category`.`name` AS category_name
                                FROM `offer_service` 
                                    INNER JOIN `service` ON `offer_service`.`service_id` = `service`.`id`
                                    INNER JOIN `category` ON `service`.`category_id` = `category`.`id`  WHERE';
                            $offer_services = $this->chatFacade->db->query($sql_services, ['offer_service.offer_id' => $offers_ids])->fetchAll();

                            $categories = [];
                            foreach ($offer_services as $vos) {
                                $categories[$vos->offer_id][$vos->category_id] = [
                                    'category_id' => $vos->category_id,
                                    'category_name' => $vos->category_name,
                                    'services' => []
                                ];
                            }
                            foreach ($offer_services as $vos) {
                                $categories[$vos->offer_id][$vos->category_id]['services'][$vos->id] = [
                                    'id' => $vos->id,
                                    'name' => $vos->name,
                                ];
                            }

                            $res = [];
                            foreach ($offers as $k => $offer) {
                                $res[$k] = [];
                                $res[$k] += ['newChatNumber' => $numberOfNewByOffer[$offer->id]];

                                foreach ($offer as $m => $valu) {
                                    $res[$k] += [$m => $valu];
                                }

                                if (!empty($categories)) {
                                    $res[$k] += ['services' => []];
                                    foreach ($categories as $offer_id => $cat) {
                                        if ($res[$k]['id'] == $offer_id) {
                                            $res[$k]['services'] = $cat;
                                        }
                                    }
                                }
                                if (!empty($offer_images)) {
                                    $res[$k] += ['thumbnails' => []];
                                    foreach ($offer_images as $j => $img) {
                                        if ($res[$k]['id'] == $img->offer_id) {
                                            //$res[$k]['thumbnails'] = base64_encode($img->thumb);
                                            $res[$k]['thumbnails'] = $img->thumb;
                                        }
                                    }
                                }
                            }

                            $latte = $this->template->getLatte();
                            $params = [
                                'offers' => $res,
                                'user' => $this->user,
                                'baseUrl' => $this->template->baseUrl,
                            ];
                            $template = APPDIR . DIRECTORY_SEPARATOR . 'UI' . DIRECTORY_SEPARATOR . 'shared_templates' . DIRECTORY_SEPARATOR . 'chat_new.latte';
                            $output = $latte->renderToString($template, $params);

                        } else {
                            $output = false;
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
            $res_mark_read = $this->markRead($message);
        }

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
