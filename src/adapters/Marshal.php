<?php
namespace adapters;

class Marshal
{
    protected $coinEmperors;
    protected $coins;
    protected $emperors;
    protected $inscriptions;
    protected $lexicon;
    protected $references;

    private $json;
    private $metals;

    public function __construct($json, $language)
    {
        $this->json = $json;
        $common = $json->loadLocalized($language, 'common');
        $this->coins = $json->setCollectionKeys($common, 'coin');
        $this->emperors = $json->setCollectionKeys($common, 'emperor');
        $this->lexicon = $common['lexicon'];
        $this->metals = $json->setCollectionKeys($common, 'metal');
    }

    public function coinEmperor($coinEmperorId)
    {
        $coinEmperor = $this->json->loadSubData('coin_emperors', $coinEmperorId);
        //$coinEmperor['images'] = $this->json->setCollectionKeys($coinEmperor, 'image', false);
        return $coinEmperor;
    }

    public function coinEmperors()
    {
        $inscriptions = $this->json->loadData('inscriptions');
        $this->inscriptions = $this->json->setCollectionKeys($inscriptions, 'inscription', false);
        $coinEmperors = $this->json->loadData('coin_emperors');
        $this->coinEmperors = $this->json->setCollectionKeys($coinEmperors, 'coin_emperor', false);
        return $this->json->loadData('coin_emperors');
    }

    public function emperors(): array
    {
        return $this->emperors;
    }

    public function lexicon()
    {
        return $this->lexicon;
    }

    public function referenceEmperors()
    {
        $references = $this->json->loadData('references');
        $this->references = $this->json->setCollectionKeys($references, 'reference', false);
        return $this->json->loadData('reference_emperors');
    }
}