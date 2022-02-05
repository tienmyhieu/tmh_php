<?php

namespace tmh_core;

use tmh_adapters\TmhDomain;
use tmh_adapters\TmhHtml;
use tmh_adapters\TmhJson;
use tmh_adapters\TmhLocalization;
use tmh_adapters\TmhRoute;

class TmhController
{
    private $domain;
    private $html;
    private $json;
    private $localization;
    private $route;

    public function __construct(TmhDomain $domain, TmhHtml $html, TmhJson $json, TmhLocalization $localization, TmhRoute $route)
    {
        $this->domain = $domain;
        $this->html = $html;
        $this->json = $json;
        $this->localization = $localization;
        $this->route = $route;
    }

    public function run()
    {
        $domains = $this->toKeyed($this->json->domains(), 'uuid');
        $inscriptions = $this->toKeyed($this->json->inscriptions(), 'uuid');
        $language = $this->domain->language($domains);
        $locale = $this->domain->locale($domains);
        $this->localization->initialize($this->json->lexicon($locale));
        $routes = $this->json->routes($locale);
        $templatePath = $this->route->template($this->toKeyed($routes, 'uuid'));
        $template = $this->json->template($templatePath);
        $routes = $this->toKeyed($routes, 'template');
        $this->html->initialize($inscriptions, $this->localization, $routes);
        return $this->html->render($template['elements'], $language);
    }

    private function toKeyed($entities, $key)
    {
        $transformed = [];
        foreach ($entities as $entity) {
            if (array_key_exists($key, $entity)) {
                $transformed[$entity[$key]] = $entity;
            }
        }
        return $transformed ?: $entities;
    }
}
