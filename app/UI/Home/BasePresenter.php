<?php

namespace App\UI\Home;

use Ijurij\Geolocation\Geolocation;
use \App\Model\ChatFacade;

abstract class BasePresenter extends \App\UI\BasePresenter
{
    protected Geolocation $geo;
    protected array $locality;

    public function __construct(protected ChatFacade $chat)
    {
        parent::__construct();
        $this->geo = new Geolocation();
        $this->locality = $this->geo->getLocality();
    }

    public function beforeRender()
    {
        parent::beforeRender();

        $this->template->geo = $this->geo->run();
        $this->template->location = $this->locality;
        if ($this->getUser()->isLoggedIn()) {
            $this->template->count_client_chat = $this->chat->countChat(client_id: $this->getUser()->getId());
        }
    }
}

class BaseTemplate extends \App\UI\BaseTemplate
{
    public string $geo;
    public array $location;
    public int $count_client_chat;
}
