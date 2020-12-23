<?php
namespace adapters;

class Language
{
    private $language;

    public function __construct()
    {
        $this->setLanguage();
    }

    public function get()
    {
        return $this->language;
    }

    private function setLanguage()
    {
        $host = $_SERVER['HTTP_HOST'];
        $hostLanguages = [
            'tienmyhieu.com' => 'vi'
        ];
        $this->language = in_array($host, $hostLanguages) ? $hostLanguages[$host] : 'vi';
    }
}