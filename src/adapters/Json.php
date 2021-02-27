<?php
namespace adapters;

class Json
{
    private $basePath = __DIR__ . '/../';

    public function load($path, $file)
    {
        $contents = '{}';
        if (file_exists($path .  '/' . $file . '.json')) {
            //echo 'opening ' . $file . '<br />';
            $contents = file_get_contents($path .  '/' . $file . '.json');
        }
        return json_decode($contents, true);
    }

    public function loadData($file)
    {
        return $this->load($this->basePath . 'data', $file);
    }

    public function loadLocalized($language, $file)
    {
        return $this->load($this->basePath . '/l10n/' . $language, $file);
    }

    public function loadLocalizedSubData($directory, $file, $language)
    {
        return $this->load($this->basePath . '/l10n/' . $language . '/' . $directory, $file);
    }

    public function loadSubData($directory, $file)
    {
        return $this->load($this->basePath . 'data/' . $directory, $file);
    }

    public function setCollectionKeys($collection, $key, $useKey=true): array
    {
        $records = $useKey ? $collection[$key] : $collection;
        $keyedCollection = [];
        foreach ($records as $item) {
            $keyedCollection[$item['uuid']] = $item;
        }
        return $keyedCollection;
    }
}