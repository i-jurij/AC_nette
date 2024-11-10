<?php

declare(strict_types=1);

namespace App\UI\Accessory;

use Nette\Utils\Strings;

trait Breadcrumb
{
    public function upperAfterDash($string)
    {
        if (empty($string)) {
            return '';
        }
        $pre = explode('-', $string);
        $first = \array_shift($pre);
        $res = '';
        foreach ($pre as $value) {
            $res .= Strings::firstUpper($value);
        }

        return $first.$res;
    }

    public function getBC(): array
    {
        $httpRequest = $this->getHttpRequest();
        $url_path = trim($httpRequest->getUrl()->path, " \/");
        $site_root = SITE_NAME.Strings::after(trim(WWWDIR, " \/"), SITE_NAME, 1);
        // request path without site root path
        $url_path_relative = trim(Strings::after($url_path, $site_root, 1), " \/");
        $controls_method_param = explode('/', $url_path_relative);
        $pre_controls = explode('.', array_shift($controls_method_param));
        $method = $this->upperAfterDash(array_shift($controls_method_param));
        $count_pre_controls = count($pre_controls);
        for ($i = 0; $i < $count_pre_controls; ++$i) {
            $ic = Strings::firstUpper($this->upperAfterDash($pre_controls[$i]));
            if ($i != 0) {
                $short = $ic;
                $full = $controls[$i - 1]['full'].$ic.':';
            } else {
                $short = $ic;
                $full = ':'.$ic.':';
            }
            $controls[$i] = [
                'short' => $short,
                'full' => $full,
            ];
        }
        if (!empty($method)) {
            $count_controls = count($controls);
            $controls[$count_controls] = [
                'short' => $method,
                'full' => $controls[$count_controls - 1]['full'].$method,
            ];
        }

        return $controls;
    }
}
