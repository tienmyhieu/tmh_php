<?php
namespace adapters;

use adapters\Marshal;
require_once (__DIR__ . '/Marshal.php');

class CoinEmperor extends Marshal
{

    public function __construct($json, $language)
    {
        parent::__construct($json, $language->get());
    }

    public function get($coinEmperorId, $country='vn')
    {
        $coinEmperor = $this->coinEmperor($coinEmperorId);
        $coinEmperor['images'] = $this->getImages($coinEmperor, $country);
        return $coinEmperor;
    }

    private function getImages($coinEmperor, $country): array
    {
        $images = [];
        foreach ($coinEmperor['reference_specimens'] as $referenceSpecimen) {
            $imageCount = 0;
            $hasMore = 2 < count($referenceSpecimen['images']);
            foreach ($referenceSpecimen['images'] as $image) {
                $image = $coinEmperor['images'][(int)$image - 1];
                $image['hasMore'] = (string)$hasMore;
                if ($country == $image['country']) {
                    if ($image['visible'] == '1' && $image['copyright'] == '0') {
                        if ($imageCount < 2) {
                            $images[] = $image;
                        }
                    }
                } else if ($image['visible'] == '1') {
                    if ($imageCount < 2) {
                        $images[] = $image;
                    }
                }
                $imageCount++;
            }
        }
        return $images;
    }
}