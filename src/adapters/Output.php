<?php
namespace adapters;

require_once(__DIR__ . '/HtmlOutput.php');

class Output
{
    private $entities;
    public function get($template): string
    {
        $output = '';
        switch ($template) {
            case 'article':
                $output = $this->article();
                break;
            case 'coin':
                $output = $this->coin();
                break;
            case 'coin_emperor':
                $output = $this->coinEmperor();
                break;
            case 'emperor':
                $output = $this->emperor();
                break;
            case 'home':
                $output = $this->home();
                break;
        }
        return $output;
    }


    public function setEntities($entities)
    {
        $this->entities = $entities;
    }

    private function article()
    {
        $article = $this->getEntity('article');
        $lexicon = $this->getEntity('lexicon');
        $output = HtmlOutput::article($article, $lexicon);
        return $output;
    }

    private function coin(): string
    {
        $coin = $this->getEntity('coin');
        $coins = $this->getEntity('coins');
        $emperors = $this->getEntity('emperors');
        $lexicon = $this->getEntity('lexicon');
        $maxims = $this->getEntity('maxims');
        $references = $this->getEntity('references');

        $images = [];
        foreach ($coin['images'] as $image) {
            $images[$image['uuid']] = $image;
        }
        $coin['images'] = $images;

        $output = HtmlOutput::twoCellStart();
        $output .= HtmlOutput::coinListTable($coins, $lexicon);
        $output .= HtmlOutput::twoCellMiddle();
        $output .= HtmlOutput::coinEmperors($coin, $emperors, $lexicon);
        $output .= HtmlOutput::twoCellEnd();
        return $output;
    }

    private function coinEmperor(): string
    {
        $articles = $this->getEntity('articles');
        $coinEmperor = $this->getEntity('coinEmperor');
        $coins = $this->getEntity('coins');
        $collections = $this->getEntity('collections');
        $emperor = $this->getEntity('emperor');
        $lexicon = $this->getEntity('lexicon');
        $references = $this->getEntity('referenceEmperors');
        $title = $this->getEntity('title');
        $variants = $this->getEntity('variants');

        $tmpImages = [];
        foreach ($coinEmperor['images'] as $image) {
            $tmpImages[$image['uuid']] = $image;
        }

        $tmpImages2 = [];
        foreach ($coinEmperor['other_images'] as $image) {
            $tmpImages2[$image['uuid']] = $image;
        }

        $output = HtmlOutput::twoCellStart();
        $output .= HtmlOutput::emperorListTable($emperor, $coins, $lexicon);
        $output .= HtmlOutput::twoCellMiddle();
        $output .= HtmlOutput::sectionTitle($lexicon['reference_images'], 3, false);
        $output .= HtmlOutput::coinEmperorReferencesList($coinEmperor['reference_specimens'], $tmpImages, $lexicon, $articles, $variants);
        //$output .= HtmlOutput::gallery($coinEmperor['images'], 4, $title, $lexicon);
        $output .= HtmlOutput::coinEmperorReferences($coinEmperor, $references, $lexicon, $articles);
        $output .= HtmlOutput::sectionTitle($lexicon['private_collection_images'], 3);
        $output .= HtmlOutput::coinEmperorReferencesList($coinEmperor['other_specimens'], $tmpImages2, $lexicon, $articles, $variants);
        //$output .= HtmlOutput::gallery($coinEmperor['other_images'], 4, $title, $lexicon);
        $output .= HtmlOutput::coinEmperorReferences2($coinEmperor['collections'], $collections, $lexicon);
        $output .= HtmlOutput::twoCellEnd();
        return $output;
    }

    private function emperor(): string
    {
        $output = HtmlOutput::twoCellStart();
        $output .= HtmlOutput::emperorListTable(
            $this->getEntity('emperor'),
            $this->getEntity('coins'),
            $this->getEntity('lexicon')
        );
        $output .= HtmlOutput::twoCellMiddle();
        $output .= 'other stuff';
        $output .= HtmlOutput::twoCellEnd();
        return $output;
    }

    private function getEntity($entity)
    {
        try {
            $this->entities[$entity];
        } catch (\Exception $e) {
            echo $entity;
        }
        return $this->entities[$entity];
    }

    private function home(): string
    {
        $emperors = $this->getEntity('emperors');
        $lexicon = $this->getEntity('lexicon');
        $output = HtmlOutput::coinEmperorTable(
            $this->getEntity('coinEmperors'),
            $emperors
        );
        $output .= HtmlOutput::referenceEmperorTable(
            $this->getEntity('referenceEmperors'),
            $emperors,
            $lexicon
        );
        $output .= HtmlOutput::articlesTable($this->getEntity('articles'), $lexicon);
        return $output;
    }

}