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
        $coinEmperor['images'] = $this->getImages($coinEmperor['reference_specimens'], $coinEmperor['images'], $country);
        $coinEmperor['other_images'] = $this->getImages($coinEmperor['other_specimens'], $coinEmperor['other_images'], $country);
        return $coinEmperor;
    }

    private function getImages($specimens, $specimenImages, $country): array
    {
        $images = [];
        foreach ($specimens as $specimen) {
            $imageCount = 0;
            $hasMore = 2 < count($specimen['images']);
            $diameter = $specimen['diameter'];
            $weight = $specimen['weight'];
            foreach ($specimen['images'] as $image) {
                $image = $specimenImages[(int)$image];
                $image['hasMore'] = (string)$hasMore;
                $image['diameter'] = $diameter;
                $image['weight'] = $weight;
                if ($country == $image['country']) {
                    if ($image['visible'] == '1' && $image['copyright'] == '0') {
                        if ($imageCount < 2) {
                            $images[] = $image;
                            $imageCount++;
                        }
                    }
                } else if ($image['visible'] == '1') {
                    if ($imageCount < 2) {
                        $images[] = $image;
                        $imageCount++;
                    }
                }
            }
        }
        return $images;
    }
}