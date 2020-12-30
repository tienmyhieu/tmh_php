<?php
namespace adapters;

class Json
{
    private $basePath = __DIR__ . '/../';

    public function load($path, $file)
    {
        return json_decode(file_get_contents($path .  '/' . $file . '.json'), true);
    }

    public function loadData($file)
    {
        return $this->load($this->basePath . 'data', $file);
    }

    public function loadLocalized($language, $file)
    {
        return $this->load($this->basePath . '/l10n/' . $language, $file);
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
//        if ($key == 'image') {
//            echo '<pre>';
//            print_r($keyedCollection);
//            echo '</pre>';
//        }
        return $keyedCollection;
    }
}