<?php
namespace core;

use adapters\Articles;
use adapters\Coin;
use adapters\CoinEmperor;
use adapters\CoinEmperors;
use adapters\Collection;
use adapters\Emperor;
use adapters\Json;
use adapters\Language;
use adapters\Meta;
use adapters\Output;
use adapters\Reference;
use adapters\ReferenceEmperors;

require_once (__DIR__ . '/../adapters/Articles.php');
require_once (__DIR__ . '/../adapters/Coin.php');
require_once (__DIR__ . '/../adapters/CoinEmperor.php');
require_once (__DIR__ . '/../adapters/CoinEmperors.php');
require_once (__DIR__ . '/../adapters/Collection.php');
require_once (__DIR__ . '/../adapters/Emperor.php');
require_once (__DIR__ . '/../adapters/Json.php');
require_once (__DIR__ . '/../adapters/Language.php');
require_once (__DIR__ . '/../adapters/Meta.php');
require_once (__DIR__ . '/../adapters/Output.php');
require_once (__DIR__ . '/../adapters/Reference.php');
require_once (__DIR__ . '/../adapters/ReferenceEmperors.php');

class Factory
{
    public function articles()
    {
        return new Articles($this->json(), $this->language());
    }

    public function coin()
    {
        return new Coin($this->json(), $this->language());
    }

    public function coinEmperor()
    {
        return new CoinEmperor($this->json(), $this->language());
    }

    public function coinEmperors()
    {
        return new CoinEmperors($this->json(), $this->language());
    }

    public function collection()
    {
        return new Collection($this->json(), $this->language());
    }

    public function emperor()
    {
        return new Emperor($this->json(), $this->language());
    }

    public function json()
    {
        return new Json();
    }

    public function language()
    {
        return new Language();
    }

    public function meta()
    {
        return new Meta($this->json(), $this->language());
    }

    public function output()
    {
        return new Output();
    }

    public function reference()
    {
        return new Reference($this->json(), $this->language());
    }

    public function referenceEmperors()
    {
        return new ReferenceEmperors($this->json(), $this->language());
    }
}