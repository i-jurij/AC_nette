<?php

declare(strict_types=1);

namespace App\UI\Politic;

use Michelf\MarkdownExtra;
use Nette;
use Nette\Application\Responses;
use Nette\Utils\Html;

final class PoliticPresenter extends \App\UI\BasePresenter // \Nette\Application\UI\Presenter
{
    // use \App\UI\Accessory\LinkFromFileSystem;

    public function renderDefault()
    {
        $path = APPDIR.'/../www/politic.md';
        $politic = '';
        if (\is_readable($path)) {
            $my_text = \file_get_contents($path);
            $my_html = MarkdownExtra::defaultTransform($my_text);

            $politic_arr = [Html::el('div')
                ->setAttribute('class', 'mx-auto mt2 p3 rounded center shadow bgcontent')
                ->appendAttribute('style', 'columns', 'auto 22em')
                ->appendAttribute('style', 'text-align', 'justify')
                ->addHtml($my_html)];
            /*
            foreach ($politic_arr as $key => $value) {
                $politic .= $value;
            }
            */
        }

        // $this->sendResponse(new Responses\TextResponse($politic));
        $this->template->pages_data = $politic_arr;
    }
}
class PoliticTemplate extends \App\UI\BaseTemplate
{
    public Nette\Security\User $user;
    public string $basePath;
    public string $baseUrl;
    public array $flashes;
    public object $presenter;
    public array $pages_data;
}
