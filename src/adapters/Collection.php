<?php
namespace adapters;

use adapters\Marshal;

require_once (__DIR__ . '/Marshal.php');

class Collection extends Marshal
{
    public function __construct($json, $language)
    {
        parent::__construct($json, $language->get());
    }

    public function get($collectionId)
    {
        return $this->collection($collectionId);
    }
}
