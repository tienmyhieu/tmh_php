<?php

namespace tmh_adapters;

class TmhJson
{
    public function domains()
    {
        return $this->load(__DIR__ . '/../tmh_resources/', 'domains');
    }

    public function inscriptions()
    {
        return $this->load(__DIR__ . '/../tmh_resources/', 'inscriptions');
    }

    public function lexicon($locale=TMH_LOCALE)
    {
        return $this->load(__DIR__ . '/../tmh_locales/' . $locale . '/', 'lexicon');
    }

    public function load($path, $file, $associative=true)
    {
        $contents = '[]';
        if ($this->exists($path .  $file . '.json')) {
            $contents = file_get_contents($path .  $file . '.json');
        }
        return json_decode($contents, $associative);
    }

    public function routes($locale=TMH_LOCALE)
    {
        return $this->load(__DIR__ . '/../tmh_locales/' . $locale . '/', 'routes');
    }

    public function template($template)
    {
        return $this->load(__DIR__ . '/../tmh_templates/', $template);
    }

    private function exists($url)
    {
        return (false !== @file_get_contents($url, 0, null, 0, 1));
    }
}
