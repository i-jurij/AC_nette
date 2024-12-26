<?php

declare(strict_types=1);

namespace App\UI\Home;

// use App\Model\PageFacade;

/**
 * @property HomeTemplate $template
 */
final class HomePresenter extends BasePresenter
{
    public function handleSaveToBackend()
    {
        $httpRequest = $this->getHttpRequest();
        if ($httpRequest->isMethod('POST')
            && !empty($httpRequest->getHeader('X_TOBACKEND'))
            && $httpRequest->getHeader('X_TOBACKEND') === 'toBackend'
            && !empty($httpRequest->getPost('city'))
            && !empty($httpRequest->getPost('region'))
            && !empty($httpRequest->getPost('city_id'))
        ) {
            $location = [
                'city' => filter_var($httpRequest->getPost('city'), FILTER_SANITIZE_SPECIAL_CHARS),
                'region' => filter_var($httpRequest->getPost('region'), FILTER_SANITIZE_SPECIAL_CHARS),
                'city_id' => filter_var($httpRequest->getPost('city_id'), FILTER_SANITIZE_SPECIAL_CHARS),
            ];
            // code for saving user location to server
            // code for getting data by location
            // $this->sendJson($this->model->getData);
            $this->sendJson('Content after city choice');
        }
    }

    public function renderDefault()
    {
        $this->template->data = 'Content before';
    }
}
class HomeTemplate extends BaseTemplate
{
    public string $data;
}
