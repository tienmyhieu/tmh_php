<?php

namespace tmh_core;

use tmh_adapters\TmhDomain;
use tmh_adapters\TmhHtml;
use tmh_adapters\TmhJson;
use tmh_adapters\TmhLocalization;
use tmh_adapters\TmhRoute;

require_once (__DIR__ . '/../tmh_adapters/TmhDomain.php');
require_once (__DIR__ . '/../tmh_adapters/TmhHtml.php');
require_once (__DIR__ . '/../tmh_adapters/TmhJson.php');
require_once (__DIR__ . '/../tmh_adapters/TmhLocalization.php');
require_once (__DIR__ . '/../tmh_adapters/TmhRoute.php');
require_once (__DIR__ . '/TmhController.php');

class TmhFactory
{
    public function domain()
    {
        return new TmhDomain();
    }

    public function controller()
    {
        return new TmhController($this->domain(), $this->html(), $this->json(), $this->localization(), $this->route());
    }

    public function html()
    {
        return new TmhHtml();
    }

    public function json()
    {
        return new TmhJson();
    }

    public function localization()
    {
        return new TmhLocalization();
    }

    public function route()
    {
        return new TmhRoute();
    }
}
