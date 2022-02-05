<?php

namespace tmh_adapters;

class TmhHtml
{
    private $elementId;
    private $elementName;
    private $inscriptions;
    /** @var TmhLocalization $localization */
    private $localization;
    private $routes;

    public function initialize($inscriptions, TmhLocalization $localization, $routes)
    {
        $this->inscriptions = $inscriptions;
        $this->localization = $localization;
        $this->routes = $routes;
    }

    public function render($elements, $language)
    {
        return '<!DOCTYPE html>' . PHP_EOL . $this->wipe($this->elements($elements), $language);
    }

    public function attributes($attributes)
    {
        $html = '';
        $this->elementId = null;
        $this->elementName = null;
        foreach ($attributes as $key => $value) {
            if ($key == 'id') {
                $this->elementId = $value;
            }
            if ($key == 'name') {
                $this->elementName = $value;
            }
            $replacement = $this->inscription($this->route($this->localization->lexicon($value)));
            $html .= ' ' . $key . '="' . $replacement . '"';
        }
        return $html;
    }

    public function childElements($element, $eol=PHP_EOL)
    {
        $closingHtml = $element['selfClosing'] ? '' : '>';
        return $element['elements'] ? '>' . $eol . $this->elements($element['elements']) : $closingHtml;
    }

    public function closeElement($element)
    {
        $eol = in_array($element['name'], ['a', 'img']) ? '' : PHP_EOL;
        return ($element['selfClosing'] ? '/>' : '</' . $element['name']. '>') . $eol;
    }

    public function elements($elements)
    {
        $html = '';
        foreach ($elements as $element) {
            $html .= $this->openElement($element);
            $html .= $this->innerHtml($element);
            $html .= $this->closeElement($element);
        }
        return $html;
    }

    public function innerHtml($element)
    {
        $replacement = $this->inscription($this->localization->lexicon($element['innerHTML']));
        $prefix =  $this->elementId ? $this->elementId . '. ' : '';
        $suffix =  $this->elementName ? ' ' . $this->elementName : '';
        $replacement = $prefix . $replacement . $suffix;
        $eol = in_array($element['name'], ['a', 'img']) ? '' : PHP_EOL;
        return strlen($element['innerHTML']) > 0 ? '>' . $replacement : $this->childElements($element, $eol);
    }

    public function inscription($inscription)
    {
        if (preg_match('|^inscriptions|', $inscription)) {
            $inscriptionParts = explode('.', $inscription);
            $inscriptionExists = array_key_exists($inscriptionParts[1], $this->inscriptions);
            $inscription = $inscriptionExists ? $this->inscriptions[$inscriptionParts[1]]['inscription'] : $inscription;
        }
        return $inscription;
    }

    public function openElement($element)
    {
        return '<' . $element['name'] . $this->attributes($element['attributes']);
    }

    public function route($route)
    {
        if (preg_match('|routes|', $route)) {
            $routeParts = explode('.', $route);
            $route = array_key_exists($routeParts[1], $this->routes) ? $this->routes[$routeParts[1]]['uuid'] : $route;
        }
        return $route;
    }

    public function wipe($html, $language)
    {
        $patterns = ['|__CDN__|', '|domain.language|', '|__DOWNLOADS__|', '|__IMAGES__|', '|__MAXIMUM_SIZE__|', '|__PREVIEW_SIZE__|', '|__UPLOADS__|', '|HTTP_HOST|'];
        $replacements = [TMH_CDN, $language, TMH_DOWNLOADS_PATH, TMH_IMAGES, TMH_IMAGE_MAXIMUM_SIZE, TMH_IMAGE_PREVIEW_SIZE, TMH_UPLOADS_PATH, TMH_HTTP_PROTOCOL . $_SERVER['HTTP_HOST']];
        return preg_replace($patterns, $replacements, $html);
    }
}
