<?php

declare(strict_types=1);

namespace App\UI\Politic;

use App\UI\Accessory\IsBot;
use Michelf\MarkdownExtra;
use Nette\Application\Responses;
use Nette\Utils\Html;

final class PoliticPresenter extends \App\UI\BasePresenter // \Nette\Application\UI\Presenter
{
    // use \App\UI\Accessory\LinkFromFileSystem;
    public function __construct(
    ) {
        $this->onStartup[] = function () {
            if (IsBot::check()) {
                $this->redirect(':Home:');
            }
        };
    }

    public function renderDefault()
    {
        $path = APPDIR.'/../www/politic.md';
        $politic = '';
        if (\is_readable($path)) {
            $my_text = \file_get_contents($path);
            $my_html = MarkdownExtra::defaultTransform($my_text);

            $politic_arr = [Html::el('div')
                ->setAttribute('class', 'mx-auto mt2 p3 rounded center shadow bgcontent')
                ->appendAttribute('style', 'columns', 'auto 30em')
                ->appendAttribute('style', 'column-rule', 'thin inset green')
                ->appendAttribute('style', 'column-gap', '2em')
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
    public string $basePath;
    public string $baseUrl;
    public array $flashes;
    public object $presenter;
    public array $pages_data;
}
