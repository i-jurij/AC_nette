<?php

declare(strict_types=1);

namespace App\UI\Home\Client;

/**
 * @property ClientTemplate $template
 */
final class ClientPresenter extends \App\UI\Home\BasePresenter
{
    public function renderDefault()
    {
        $this->template->data = 'Client presenter';
    }
}

class ClientTemplate extends \App\UI\Home\BaseTemplate
{
    public string $data;
}
