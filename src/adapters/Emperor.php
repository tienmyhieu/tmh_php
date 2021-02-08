<?php
namespace adapters;

use adapters\Marshal;

require_once (__DIR__ . '/Marshal.php');

class Emperor extends Marshal
{
    public function __construct($json, $language)
    {
        parent::__construct($json, $language->get());
    }

    public function get($emperorId)
    {
        return $this->emperor($emperorId);
    }
}
