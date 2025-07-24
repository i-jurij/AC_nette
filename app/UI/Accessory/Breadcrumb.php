<?php

namespace App\UI\Accessory;

use Nette\Utils\Strings;

/**
 * Only for routes type $router->addRoute(presenter/action[id], 'Home:default').
 */
trait Breadcrumb
{
    public function upperAfterDash(?string $string = null): string
    {
        if (empty($string)) {
            return '';
        }
        $pre = \explode('-', $string);
        $first = \array_shift($pre);
        $res = '';
        foreach ($pre as $value) {
            $res .= Strings::firstUpper($value);
        }

        return $first . $res;
    }

    public function getBC(): array
    {
        $httpRequest = $this->getHttpRequest();
        $url = $httpRequest->getUrl();

        $url_host = $url->getHost();
        $url_path = trim($url->getPath(), " \/");
        // $url_query = $httpRequest->getQuery();
        // $url_fragment = $url->getFragment();
        // $method = $httpRequest->getMethod();

        if ($url_host === SITE_NAME) {
            $site_root = SITE_NAME;
            $url_path_relative = $url_path;
        } else {
            $site_root = SITE_NAME . Strings::after(\trim(WWWDIR, " \/"), SITE_NAME, 1);
            // request path without site root path
            $url_path_relative = Strings::after($url_path, $site_root, 1);
        }

        if (!empty($url_path_relative)) {
            $controls_method_param = explode('/', $url_path_relative);
        } else {
            $controls_method_param = [];
        }

        $as = array_shift($controls_method_param);
        if (!empty($as)) {
            $pre_controls = explode('.', $as);
        } else {
            $pre_controls = [];
        }

        $count_pre_controls = count($pre_controls);

        for ($i = 0; $i < $count_pre_controls; ++$i) {
            $ic = Strings::firstUpper($this->upperAfterDash($pre_controls[$i]));
            if ($i != 0) {
                $short = $ic;
                $full = $controls[$i - 1]['full'] . $ic . ':';
            } else {
                $short = $ic;
                $full = ':' . $ic . ':';
            }
            $controls[$i] = [
                'short' => $short,
                'full' => $full,
            ];
        }

        $method = $this->upperAfterDash(array_shift($controls_method_param));
        if (!empty($method)) {
            $count_controls = count($controls);
            $controls[$count_controls] = [
                'short' => $method,
                'full' => $controls[$count_controls - 1]['full'] . $method,
            ];
        }
        /*
        $param = \end($controls_method_param);
        if (!empty($param)) {
            \array_push($controls, [
                'short' => $param,
                'full' => '',
            ]);
        }
        */

        return $controls ?? [];
    }
}
