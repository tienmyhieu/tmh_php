<?php
namespace adapters;

require_once(__DIR__ . '/HtmlOutput.php');

class Output
{
    private $entities;
    private $title;
    public function get($title, $template): string
    {
        $this->title = $title;
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
            case 'collection':
                $output = $this->collection();
                break;
            case 'emperor':
                $output = $this->emperor();
                break;
            case 'home':
                $output = $this->home();
                break;
            case 'reference':
                $output = $this->reference();
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
        $output = $this->title() . HtmlOutput::article($article, $lexicon);
        return $output;
    }

    private function coin(): string
    {
        $coin = $this->getEntity('coin');
        $coins = $this->getEntity('coins');
        $emperors = $this->getEntity('emperors');
        $inscriptions = $this->getEntity('inscriptions');
        $lexicon = $this->getEntity('lexicon');
        $descriptions = $this->getEntity('descriptions');
        $references = $this->getEntity('references');

        $images = [];
        if (in_array('images', array_keys($coin))) {
            foreach ($coin['images'] as $image) {
                $images[$image['uuid']] = $image;
            }
        }
        $coin['images'] = $images;
        $coin['title'] = $this->getEntity('title');

        $titleSuffix = ' (' . $lexicon['meaning'] . ')';
        $entityReferences = in_array('references', array_keys($descriptions)) ? $descriptions['references'] : [];
        $output = $this->title() . HtmlOutput::twoCellStart();
        $output .= HtmlOutput::coinListTable($coins, $lexicon);
        $output .= HtmlOutput::twoCellMiddle();
        $output .= HtmlOutput::coinEmperors($coin, $emperors, $inscriptions);
        $output .= HtmlOutput::maximsTable($coin, $descriptions, $references, $lexicon);
        $output .= HtmlOutput::references($lexicon, $entityReferences, $references, $titleSuffix);
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
        $hasVariants = false;
        if (in_array('thong_variants', array_keys($coinEmperor))) {
            $hasVariants = (bool)$coinEmperor['thong_variants'];
        }

        $tmpImages = [];
        foreach ($coinEmperor['images'] as $image) {
            $tmpImages[$image['uuid']] = $image;
        }

        $tmpImages2 = [];
        foreach ($coinEmperor['other_images'] as $image) {
            $tmpImages2[$image['uuid']] = $image;
        }
        $output = $this->title() . HtmlOutput::twoCellStart();
        $output .= HtmlOutput::emperorListTable($emperor, $coins, $lexicon);
        $output .= HtmlOutput::twoCellMiddle();
        $output .= HtmlOutput::sectionTitle($lexicon['reference_images'], 3, false);
        $output .= HtmlOutput::coinEmperorReferencesList(
            $coinEmperor['reference_specimens'],
            $tmpImages,
            $lexicon,
            $articles,
            $variants, $coinEmperor['inscriptions'],
            $title,
            $hasVariants
        );
        //$output .= HtmlOutput::gallery($coinEmperor['images'], 4, $title, $lexicon);
        $output .= HtmlOutput::coinEmperorReferences($coinEmperor, $references, $lexicon, $articles);
        $output .= HtmlOutput::sectionTitle($lexicon['private_collection_images'], 3);
        $output .= HtmlOutput::coinEmperorReferencesList(
            $coinEmperor['other_specimens'],
            $tmpImages2,
            $lexicon,
            $articles,
            $variants,
            $coinEmperor['inscriptions'],
            $title,
            $hasVariants
        );
        //$output .= HtmlOutput::gallery($coinEmperor['other_images'], 4, $title, $lexicon);
        $output .= HtmlOutput::coinEmperorReferences2($coinEmperor['collections'], $collections, $lexicon);
        $output .= HtmlOutput::twoCellEnd();
        return $output;
    }

    private function collection(): string
    {
        $coins = $this->getEntity('coins');
        $lexicon = $this->getEntity('lexicon');
        $collection = $this->getEntity('collection');
        return $this->title() .
            $this->collectionParentTitle($collection, 5) .
            HtmlOutput::collection($collection, $coins, $lexicon);
    }

    private function localizeKeys($keyValuePairs, $locales): array
    {
        $localized = [];
        foreach ($keyValuePairs as $key => $value) {
            $localized[$locales[$key]] = $value;
        }
        return $localized;
    }

    private function emperor(): string
    {
        $coins = $this->getEntity('coins');
        $emperor = $this->getEntity('emperor');
        $title = $this->getEntity('title');
        return $this->title() . HtmlOutput::emperorImages($emperor, $coins, $title);
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
        $output = $this->title() . HtmlOutput::coinEmperorTable(
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

    private function reference(): string
    {
        $lexicon = $this->getEntity('lexicon');
        $reference = $this->getEntity('reference');
        if (in_array('bibliography', array_keys($reference))) {
            $reference['bibliography'] = $this->localizeKeys($reference['bibliography'], $lexicon);
        }
        return $this->title() . HtmlOutput::reference($reference, $lexicon);
    }

    private function title()
    {
        return '<h2 style="margin: 0">' . $this->title . '</h2>';
    }

    private function collectionParentTitle($collection, $size)
    {
        $parentTitle = '';
        if (in_array('parent_title', array_keys($collection['attributes']))) {
            if (0 < strlen($collection['attributes']['parent_title'])) {
                $title = $collection['attributes']['parent_title'];
                $parentTitle .= '<h' . $size . ' style="margin: 0;"> - ' . $title . '</h' . $size . '><br />';
            }
        }
        return $parentTitle;
    }
}