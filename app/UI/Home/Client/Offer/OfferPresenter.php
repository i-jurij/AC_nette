<?php

declare(strict_types=1);

namespace App\UI\Home\Client\Offer;

/**
 * @property OfferTemplate $template
 */
final class OfferPresenter extends \App\UI\Home\BasePresenter
{
    use \App\UI\Accessory\RequireLoggedUser;

    public function renderDefault()
    {
        $this->template->data = 'Offer presenter';
    }

    public function renderAdd()
    {
        $this->template->data = 'Offer add presenter';
    }
}

class OfferTemplate extends \App\UI\Home\BaseTemplate
{
    public string $data;
}
