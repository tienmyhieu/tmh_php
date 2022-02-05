<?php

namespace tmh_adapters;

class TmhLocalization
{
    private $lexicon;

    public function initialize($lexicon)
    {
        $this->lexicon = $lexicon;
    }

    public function lexicon($lexicon)
    {
        if (preg_match('|lexicon|', $lexicon)) {
            $lexiconValues = '';
            foreach (explode(',', $lexicon) as $lexiconValue) {
                $lexiconParts = explode('.', $lexiconValue);
                $lexiconValues .= $this->phrase($lexiconParts[1]) . ',';
            }
            $lexiconValues = substr($lexiconValues, 0, -1);
            $lexicon = $lexiconValues;
        }
        return $lexicon;
    }

    public function phrase($key)
    {
        return array_key_exists($key, $this->lexicon) ? $this->lexicon[$key] : $key;
    }
}
