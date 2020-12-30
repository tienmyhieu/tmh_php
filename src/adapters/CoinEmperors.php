<?php
namespace adapters;

use adapters\Marshal;
require_once (__DIR__ . '/Marshal.php');

class CoinEmperors extends Marshal
{
    public function __construct($json, $language)
    {
        parent::__construct($json, $language->get());
    }

    public function get(): array
    {
        $coinEmperors = [];
        $emperors = [];
        $previousInscription = 0;
        foreach ($this->coinEmperors() as $coinEmperor) {
            $inscription = $this->getInscription($coinEmperor);
            $currentInscription = $coinEmperor['inscription_uuid'];
            if ($currentInscription != $previousInscription) {
                $emperors = [];
            }
            $emperors[$coinEmperor['emperor_uuid']] = $this->emperors[$coinEmperor['emperor_uuid']];
            $emperors[$coinEmperor['emperor_uuid']]['count'] = $coinEmperor['count'];
            $emperors[$coinEmperor['emperor_uuid']]['href'] .= '_'. $this->coins[$coinEmperor['coin_uuid']]['href'];
            $coinEmperors[$coinEmperor['coin_uuid']][$coinEmperor['inscription_uuid']] = [
                'coin' => $this->coins[$coinEmperor['coin_uuid']]['name'],
                'coin_href' => $this->coins[$coinEmperor['coin_uuid']]['href'],
                'inscription' => $inscription,
                'emperors' => $emperors
            ];
            $previousInscription = $currentInscription;
        }
        return $coinEmperors;
    }

    public function getEmperorByCoinEmperorId($coinEmperorId): array
    {
        $coinEmperors = $this->coinEmperors();
        $emperors = $this->emperors();
        foreach ($coinEmperors as $coinEmperor) {
            if ($coinEmperor['uuid'] == $coinEmperorId) {
                return $emperors[$coinEmperor['emperor_uuid']];
            }
        }
    }

    public function getEmperorByName($emperorName): array
    {
        $emperorName = str_replace('|thông_bảo|', '', $emperorName);
        $emperorName = str_replace('|_|', '', $emperorName);
        foreach ($this->emperors() as $emperor) {
            if (mb_strpos(mb_strtolower($emperorName), mb_strtolower($emperor['name'])) !== false) {
                return $emperor;
            }
        }
        return [];
    }

    public function getEmperorList($emperorId)
    {
        $list = [];
        $emperor = $this->emperors[$emperorId];
        foreach ($this->coinEmperors() as $coinEmperor) {
            $inscription = $this->getInscription($coinEmperor);
            if ($coinEmperor['emperor_uuid'] == $emperorId && $coinEmperor['count'] > 0) {
                $list[] = [
                    'coin' => $this->coins[$coinEmperor['coin_uuid']]['name'],
                    'href' => $emperor['href'] . '_' .  $this->coins[$coinEmperor['coin_uuid']]['href'] ,
                    'count' => $coinEmperor['count'],
                    'inscription' => $inscription,
                ];
            }
        }
        return $list;
    }

    private function getInscription($coinEmperor): string
    {
        $hasInscription = in_array($coinEmperor['inscription_uuid'], array_keys($this->inscriptions));
        return $hasInscription ? $this->inscriptions[$coinEmperor['inscription_uuid']]['inscription'] : '';
    }
}