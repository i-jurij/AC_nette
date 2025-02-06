<?php

declare(strict_types=1);

namespace App\UI\Home;

// use App\Model\PageFacade;

/**
 * @property HomeTemplate $template
 */
final class HomePresenter extends BasePresenter
{
    public function actionSaveToBackend()
    {
        $httpRequest = $this->getHttpRequest();

        $data = '';
        if ($httpRequest->isMethod('POST')
            && !empty($httpRequest->getPost('city'))
            && !empty($httpRequest->getPost('region'))
        ) {
            $city = filter_var($httpRequest->getPost('city'), FILTER_SANITIZE_SPECIAL_CHARS);
            $region = filter_var($httpRequest->getPost('region'), FILTER_SANITIZE_SPECIAL_CHARS);

            $location = [
                'city' => $city,
                'region' => $region,
            ];
            // code for saving user location to server
            // code for getting data by location
            // $this->sendJson($this->model->getData);
            $data .= $city.'<br>'.$region.'<br>Content after city choice';
        }

        $this->sendJson($data);
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
