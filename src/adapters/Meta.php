<?php
namespace adapters;

class Meta
{
    private $identifier;
    private $language;
    private $template;
    private $title;
    public function __construct($json, $language)
    {
        $this->language = $language->get();
        $this->setTitleFields($json);
    }

    public function fields(): array
    {
        return [
            'identifier' => $this->identifier,
            'language' => $this->language,
            'template' => $this->template,
            'title' => $this->title
        ];
    }

    public function identifier()
    {
        return $this->identifier;
    }

    public function language()
    {
        return $this->language;
    }

    public function template()
    {
        return $this->template;
    }

    public function title()
    {
        return $this->title;
    }

    private function setTitleFields($json)
    {
        $languageTitles = $json->loadLocalized($this->language, 'titles');
        $titles = [];
        foreach ($languageTitles as $languageTitle) {
            $titles[$languageTitle['href']] = [
                'identifier' => $languageTitle['identifier'],
                'template' => $languageTitle['template'],
                'title' => $languageTitle['title']
            ];
        }
        $urlTitle = $this->urlTitle();
        $this->identifier = in_array($urlTitle, array_keys($titles)) ? $titles[$urlTitle]['identifier'] : '0';
        $this->template = in_array($urlTitle, array_keys($titles)) ? $titles[$urlTitle]['template'] : 'home';
        $this->title = in_array($urlTitle, array_keys($titles)) ? $titles[$urlTitle]['title'] : $titles[''];
    }

    private function urlTitle(): string
    {
        parse_str($_SERVER['REDIRECT_QUERY_STRING'], $fields);
        return trim($fields['title']);
    }
}
