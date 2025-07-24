<?php

namespace App\UI\Accessory;

class IsBot
{
    /**
     * function for bots check.
     */
    public const BOTS = [
        'bot',
        'crawler',
        'curl',
        'parser',
        'spider',
        'python-requests',
        'Monitoring',
        'Accoona',
        'Analyzer',
        'Ask Jeeves',
        'a.pr-cy.ru',
        'Baiduspider',
        'DomainVader',
        'facebookexternalhit',
        'findlinks',
        'google',
        'heritrix',
        'Ezooms',
        'ia_archiver',
        'ltx71',
        'Nigma.ru',
        'OpenindexSpider',
        'proximic',
        'PEAR',
        'Riddler',
        'SiteStatus',
        'SISTRIX',
        'StackRambler',
        'statdom.ru',
        'Spider',
        'Snoopy',
        'slurp',
        'vkShare',
        'W3C_Validator',
        'WebAlta',
        'YahooFeedSeeker',
        'Yahoo!',
        'yandex',
        'Yeti',
    ];

    /**
     * function for bots check.
     */
    public static function check(): bool
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        if (empty($user_agent)) {
            return false;
        }
        foreach (self::BOTS as $bot) {
            if (stripos($user_agent, $bot) !== false) {
                return true;
            }
        }

        return false;
    }
}
