<?php
namespace adapters;

use adapters\Marshal;
require_once (__DIR__ . '/Marshal.php');

class ReferenceEmperors extends Marshal
{
    public function __construct($json, $language)
    {
        parent::__construct($json, $language->get());
    }

    public function get(): array
    {
        $referenceEmperors = [];
        $previousReferenceId = 0;
        $patterns = ['|\.|', '|\,|', "|\'|", '|\s+|'];
        $replacements = ['', '', '', '_'];
        foreach ($this->referenceEmperors() as $referenceEmperor) {
            $referenceYear = $this->references[$referenceEmperor['reference_uuid']]['year'];
            $year = 0 < strlen($referenceYear) ? $referenceYear : date('Y');
            $currentReferenceId = (int)$referenceEmperor['reference_uuid'];
            if ($currentReferenceId != $previousReferenceId) {
                $emperors = [];
            }
            $emperors[$referenceEmperor['emperor_uuid']] = $this->emperors[$referenceEmperor['emperor_uuid']];
            $emperors[$referenceEmperor['emperor_uuid']]['count'] = $referenceEmperor['count'];
            $href =  preg_replace($patterns, $replacements, $this->references[$referenceEmperor['reference_uuid']]['title']);
            $emperors[$referenceEmperor['emperor_uuid']]['href'] = $href . '#' . $emperors[$referenceEmperor['emperor_uuid']]['href'];
            $referenceEmperors[$referenceEmperor['reference_uuid']] = [
                'acronym' => $this->references[$referenceEmperor['reference_uuid']]['acronym'],
                'href' => $href,
                'year' => $year,
                'title' => $this->references[$referenceEmperor['reference_uuid']]['title'],
                'emperors' => $emperors,
                'author' => $this->references[$referenceEmperor['reference_uuid']]['author'],
                //'url' => $this->references[$referenceEmperor['reference_uuid']]['url']
            ];
            $previousReferenceId = $currentReferenceId;
        }
        return $referenceEmperors;
    }
}