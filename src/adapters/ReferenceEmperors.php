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
        foreach ($this->referenceEmperors() as $referenceEmperor) {
            $referenceYear = $this->references[$referenceEmperor['reference_id']]['year'];
            $year = 0 < strlen($referenceYear) ? $referenceYear : date('Y');
            $currentReferenceId = (int)$referenceEmperor['reference_id'];
            if ($currentReferenceId != $previousReferenceId) {
                $emperors = [];
            }
            $emperors[$referenceEmperor['emperor_id']] = $this->emperors[$referenceEmperor['emperor_id']];
            $emperors[$referenceEmperor['emperor_id']]['count'] = $referenceEmperor['count'];
            $emperors[$referenceEmperor['emperor_id']]['href'] = $this->references[$referenceEmperor['reference_id']]['acronym'] . '#' . $emperors[$referenceEmperor['emperor_id']]['href'];
            $referenceEmperors[$referenceEmperor['reference_id']] = [
                'acronym' => $this->references[$referenceEmperor['reference_id']]['acronym'],
                'href' => $this->references[$referenceEmperor['reference_id']]['acronym'],
                'year' => $year,
                'title' => $this->references[$referenceEmperor['reference_id']]['title'],
                'emperors' => $emperors
            ];
            $previousReferenceId = $currentReferenceId;
        }
        return $referenceEmperors;
    }
}