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

    private function coinEmperor()
    {
        $output = HtmlOutput::twoCellStart();
        $output .= HtmlOutput::emperorListTable(
            $this->getEntity('emperor'),
            $this->getEntity('coins'),
            $this->getEntity('lexicon')
        );
        $coinEmperor = $this->getEntity('coinEmperor');
        $references = $this->getEntity('referenceEmperors');
        $output .= HtmlOutput::twoCellMiddle();
        $output .= HtmlOutput::gallery($coinEmperor['images'], 4, $this->getEntity('title'));
        $output .= HtmlOutput::coinEmperorReferences($coinEmperor, $references, $this->getEntity('lexicon'));
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
        $output = HtmlOutput::coinEmperorTable(
            $this->getEntity('coinEmperors'),
            $this->getEntity('emperors')
        );
        $output .= HtmlOutput::referenceEmperorTable(
            $this->getEntity('referenceEmperors'),
            $this->getEntity('emperors'),
            $this->getEntity('lexicon')
        );
        return $output;
    }

}