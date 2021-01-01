<?php
namespace adapters;

use adapters\Marshal;

require_once (__DIR__ . '/Marshal.php');

class Articles extends Marshal
{
    public function __construct($json, $language)
    {
        parent::__construct($json, $language->get());
    }

    public function get()
    {
        return $this->articles();
    }

    public function getArticle($articleId, $language)
    {
        return $this->article($articleId, $language);
    }
}
