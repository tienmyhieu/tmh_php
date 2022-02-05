<?php

namespace tmh_adapters;

class TmhDomain
{
    public function language($domains)
    {
        $domainExists = array_key_exists($_SERVER['HTTP_HOST'], $domains);
        $domain = $domainExists ? $domains[$_SERVER['HTTP_HOST']] : $domains[TMH_DOMAIN];
        return substr($domain['locale'], 0, 2);
    }

    public function locale($domains)
    {
        $domainExists = array_key_exists($_SERVER['HTTP_HOST'], $domains);
        return $domainExists ? $domains[$_SERVER['HTTP_HOST']]['locale'] : $domains[TMH_DOMAIN]['locale'];
    }
}
