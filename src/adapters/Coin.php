<?php
namespace adapters;

use adapters\Marshal;

require_once (__DIR__ . '/Marshal.php');

class Coin extends Marshal
{
    public function __construct($json, $language)
    {
        parent::__construct($json, $language->get());
    }

    public function get($coinId)
    {
        return $this->coin($coinId);
    }

    public function getMaxims($coinId)
    {
        return $this->maxims($coinId);
    }
}
