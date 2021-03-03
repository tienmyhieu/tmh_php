<?php
namespace adapters;

class Marshal
{
    protected $articles;
    protected $coinEmperors;
    protected $coins;
    protected $collections;
    protected $emperors;
    protected $inscriptions;
    protected $language;
    protected $lexicon;
    protected $references;
    protected $titles;
    protected $variants;

    private $json;
    private $metals;

    public function __construct($json, $language)
    {
        $this->json = $json;
        $this->language = $language;
        $common = $json->loadLocalized($language, 'common');
        $this->articles = $json->setCollectionKeys($common, 'article');
        $this->coins = $json->setCollectionKeys($common, 'coin');
        $this->emperors = $json->setCollectionKeys($common, 'emperor');
        $this->lexicon = $common['lexicon'];
        $this->metals = $json->setCollectionKeys($common, 'metal');
        $this->variants = $json->setCollectionKeys($common, 'variants');
    }

    public function articles()
    {
        return $this->articles;
    }

    public function article($articleId, $language)
    {
        $locales = $this->json->loadLocalizedSubData('articles', $articleId, $language);
        $article = $this->json->loadSubData('articles', $articleId);
        return $this->transformArticle($article, $locales);
    }

    public function coin($coinId)
    {
        return $this->json->loadSubData('coins', $coinId);
    }

    public function coins()
    {
        return $this->coins;
    }

    public function coinEmperor($coinEmperorId)
    {
        $coinEmperor = $this->json->loadSubData('coin_emperors', $coinEmperorId);
        $coinEmperor['images'] = $this->json->setCollectionKeys($coinEmperor['images'], 'image', false);
        $coinEmperor['other_images'] = $this->json->setCollectionKeys($coinEmperor['other_images'], 'image', false);
        return $coinEmperor;
    }

    public function coinEmperors()
    {
        $this->inscriptions();
        $coinEmperors = $this->json->loadData('coin_emperors');
        $this->coinEmperors = $this->json->setCollectionKeys($coinEmperors, 'coin_emperor', false);
        return $this->json->loadData('coin_emperors');
    }

    public function collection($collectionId)
    {
        return $this->loadCollection($collectionId);
    }

    private function titledEntities($entities, $titles): array
    {
        $tmpEntities = [];
        foreach ($entities as $uuid => $entity) {
            if (in_array('title', array_keys($entity))) {
                if (0 < strlen($entity['title'])) {
                    $title = $titles[$entity['title']];
                    $entity['title'] = $title['title'];
                }
                $tmpEntities[$uuid] = $entity;
            }
        }
        return $tmpEntities;
    }

    private function loadCollection($collectionId)
    {
        $titles = $this->titles();
        $collection = $this->json->loadSubData('collections', $collectionId);
        $collection = $this->transformEntityCollections($collection, $titles);
        if (in_array('attributes', array_keys($collection))) {
            if (in_array('parent_title', array_keys($collection['attributes']))) {
                if (0 < strlen($collection['attributes']['parent_title'])) {
                    $parentTitle = $titles[$collection['attributes']['parent_title']];
                    $collection['attributes']['parent_title'] = $parentTitle['title'];
                }
            }
        }
        $collection['collections'] = $this->expandCollections($collection['collections']);
        return $collection;
    }

    private function expandCollections($collections, $checkTitle=false)
    {
        $expandedCollections = [];
        foreach ($collections as $uuid => $subCollection) {
            $tmpCollection = $subCollection;
            if ($subCollection['expand']) {
                if ($checkTitle) {
                    $hasTitle = 0 < strlen($subCollection['title']);
                    if (!$hasTitle) {
                        $tmpCollection = $this->loadCollection($uuid);
                    }
                } else {
                    $tmpCollection = $this->loadCollection($uuid);
                }
            }
            $expandedCollections[$uuid] = $tmpCollection;
        }
        return $expandedCollections;
    }

    public function collections()
    {
        $collections = $this->json->loadData('collections');
        $this->collections = $this->json->setCollectionKeys($collections, 'collection', false);
        return $this->collections;
    }

    public function emperor($emperorId)
    {
        $emperor = $this->json->loadSubData('emperors', $emperorId);
        $emperor['images'] = $this->transformEntityCollection($emperor, 'images');
        return $emperor;
    }

    public function emperors(): array
    {
        return $this->emperors;
    }

    public function inscriptions()
    {
        $inscriptions = $this->json->loadData('inscriptions');
        $this->inscriptions = $this->json->setCollectionKeys($inscriptions, 'inscription', false);
    }

    public function getInscriptions(): array
    {
        $this->inscriptions();
        return $this->inscriptions;
    }

    public function lexicon()
    {
        return $this->lexicon;
    }

    public function descriptions($coinId)
    {
        return $this->json->loadLocalizedSubData('descriptions', $coinId, $this->language);
    }

    public function reference($referenceId)
    {
        $titles = $this->titles();
        $reference = $this->json->loadSubData('references', $referenceId);
        $reference = $this->transformEntityCollections($reference, $titles);
        $reference['expanded_collections'] = $this->expandCollections($reference['collections'], true);
        return $reference;
    }

    private function transformEntityCollections($entity, $titles)
    {
        $entity['images'] = $this->transformEntityCollection($entity, 'images');
        $entity['images'] = $this->titledEntities($entity['images'], $titles);
        $entity['original_images'] = $this->transformEntityCollection($entity, 'original_images');
        $entity['original_images'] = $this->titledEntities($entity['original_images'], $titles);
        $entity['collections'] = $this->transformEntityCollection($entity, 'collections');
        $entity['collections'] = $this->titledEntities($entity['collections'], $titles);
        if (in_array('specimens', array_keys($entity))) {
            $entity['specimens'] = $this->setKeyedItems($entity['specimens']);
        }
        return $entity;
    }

    public function referenceEmperors()
    {
        $this->references = $this->references();
        return $this->json->loadData('reference_emperors');
    }

    public function references()
    {
        $references = $this->json->loadData('references');
        return $this->json->setCollectionKeys($references, 'reference', false);
    }

    public function variants()
    {
        return $this->variants;
    }

    public function titles()
    {
        if (!$this->titles) {
            $this->titles = $this->json->loadLocalized($this->language, 'titles');
            $this->titles = $this->setKeyedItems($this->titles);
        }
        return $this->titles;
    }

    private function itemsKey($items, $key): string
    {
        return in_array($key, array_keys($items)) ? $items[$key] : '';
    }

    private function setKeyedItems($items): array
    {
        $keyedItems = [];
        foreach ($items as $item) {
            $keyedItems[$item['uuid']] = $item;
        }
        return $keyedItems;
    }

    private function transformArticle($article, $locales): array
    {
        $transformed = [];
        $images = $this->setKeyedItems($article['images']);
        $sections = $this->setKeyedItems($article['sections']);
        $sentences = $this->setKeyedItems($locales['sentences']);
        $titles = $this->setKeyedItems($locales['titles']);
        foreach ($images as $uuid => $image) {
            $title = $this->itemsKey($image, 'title_uuid');
            $transformed['images'][$uuid]['title'] = $title ? $titles[$title]['title'] : '';
            $transformed['images'][$uuid]['src'] = $image['src'];
        }
        $transformed['intro']['images'] = [];
        foreach ($article['intro']['images'] as $introImage) {
            $transformed['intro']['images'][$introImage]['src'] = $images[$introImage]['src'];
            $transformed['intro']['images'][$introImage]['title'] = $titles[$images[$introImage]['title_uuid']]['title'];
        }
        $transformed['intro']['image_dir'] = $article['intro']['image_dir'];
        $transformed['intro']['sentences'] = [];
        $transformed['sections'] = [];
        foreach ($article['intro']['sentences'] as $sentence) {
            $transformed['intro']['sentences'][$sentence] = $sentences[$sentence]['sentence'];
        }
        foreach ($sections as $uuid => $section) {
            $title = $this->itemsKey($section, 'title_uuid');
            echo $title;
            $transformed['sections'][$uuid]['title'] = $title ? $titles[$title]['title'] : '';
            $transformed['sections'][$uuid]['images'] = [];
            $transformed['sections'][$uuid]['sentences'] = [];
            foreach ($section['sentences'] as $sentence) {
                $transformed['sections'][$uuid]['sentences'][$sentence] = $sentences[$sentence]['sentence'];
            }
            foreach ($section['images'] as $image) {
                $transformed['sections'][$uuid]['images'][$image]['src'] = $transformed['images'][$image]['src'];
                $transformed['sections'][$uuid]['images'][$image]['title'] = $transformed['images'][$image]['title'];
            }
        }
        unset($transformed['images']);
        return $transformed;
    }

    private function transformEntityCollection($entity, $collection): array
    {
        $collectionItems = [];
        if (is_array($entity) && 0 < count($entity)) {
            if (in_array($collection, array_keys($entity))) {
                foreach ($entity[$collection] as $collectionItem) {
                    $collectionItems[$collectionItem['uuid']] = $collectionItem;
                }
            }
        }
        return $collectionItems;
    }
}