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

    public function collections()
    {
        $collections = $this->json->loadData('collections');
        $this->collections = $this->json->setCollectionKeys($collections, 'collection', false);
        return $this->collections;
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
}