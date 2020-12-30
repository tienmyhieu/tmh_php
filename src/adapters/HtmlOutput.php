<?php
namespace adapters;

class HtmlOutput
{
    public static function emperorHeaderRow($emperors, $leadingCells): string
    {
        $html = '';
        $html .= "\n\t" . '<tr>';
        for ($i = 0; $i < $leadingCells; $i++) {
            $html .= "\n\t\t" . '<th></th>';
        }
        foreach($emperors as $emperorId => $emperor) {
            $html .= "\n\t\t" . '<th align="left">' .
                '<a href="' . $emperor['href'] . '" title="' . $emperor['name'] . '">' . $emperor['name'] . '</a>' .
                '</th>';
        }
        $html .= "\n\t" . '</tr>';
        return $html . "\n";
    }

    public static function coinEmperorRows($coinEmperors, $emperors): string
    {
        $html = '';
        $previousCoin = '';
        $i = 1;
        foreach ($coinEmperors as $coinId => $coinInscription) {
            $html .= "\n\t" . '<tr>' . "\n\t\t" . '<td>' .  $i . '</td>';
            foreach ($coinInscription as $inscriptionId => $inscription) {
                $currentCoin = $inscription['coin'];
                if ($currentCoin != $previousCoin) {
                    $coinTitle = self::linkTitle($inscription['coin']);
                    $html .= "\n\t\t" . '<td><a href="' . $inscription['coin_href'] . '" title="' . $coinTitle . '">' .
                        $inscription['coin'] . '</a></td>';
                }
                $previousCoin = $currentCoin;
                foreach($inscription['emperors'] as $emperorId => $emperor) {
                    $emperor = $inscription['emperors'][$emperorId];
                    $emperorTitle = self::linkTitle($emperor['href']);
                    $html .= "\n\t\t" . '<td>';
                    if (0 < $emperor['count']) {
                        $html .= '<a href="' . $emperor['href'] . '" title="' . $emperorTitle . '">' .
                            $inscription['inscription'] . '</a>';
                    }
                    $html .= '</td>';
                }
            }
            $html .= "\n\t" . '</tr>';
            $i++;
        }
        return $html . "\n";
    }

    public static function coinEmperorReferences2($collection, $sources, $lexicon): string
    {
        $title = self::sectionTitle($lexicon['private_collections']);
        $html = $title . "\n\t\t\t" . '<table width="100%" border="1" cellpadding="2" cellspacing="1">';
        $html .= "\n\t\t\t\t" . '<tbody>';
        foreach ($collection as $item) {
            $hasObverse = 0 < strlen($item['url_obverse']);
            $hasReverse = 0 < strlen($item['url_reverse']);
            $obvUrl = $hasObverse ? '<a href="' . $item['url_obverse'] . '" target="_blank">' . $lexicon['obverse'] . '</a>' : '';
            $revUrl = $hasReverse ? '<a href="' . $item['url_reverse'] . '" target="_blank">' . $lexicon['reverse'] . '</a>' : '';
            $thisYear =  date('Y');
            $source = $sources[$item['collection_id']];
            $year = 0 < strlen($source['year']) ? $source['year'] : $thisYear;
            $year = preg_replace('|9999|', $thisYear, $year);
            $html .= "\n\t\t\t\t\t" . '<tr>';
            $html .= "\n\t\t\t\t\t\t" . '<td>' . $item['date'] . '</td>';
            $html .= "\n\t\t\t\t\t\t" . '<td>' . $source['acronym'] . '</td>';
            $html .= "\n\t\t\t\t\t\t" . '<td>' . $item['lot_number'] . '</td>';
            $html .= "\n\t\t\t\t\t\t" . '<td align="left">' . $source['title'] . '</td>';
            $html .= "\n\t\t\t\t\t\t" . '<td>' . $obvUrl . '</td>';
            $html .= "\n\t\t\t\t\t\t" . '<td>' . $revUrl . '</td>';
            $html .= "\n\t\t\t\t\t" . '</tr>';
        }
        $html .= "\n\t\t\t\t" . '</tbody>';
        $html .= "\n\t\t\t" . '</table>';
        return $html;
    }

    public static function coinEmperorReferences($coinEmperor, $references, $lexicon): string
    {
        $title = self::sectionTitle($lexicon['references']);
        $html = $title . "\n\t\t\t" . '<table width="100%" border="1" cellpadding="2" cellspacing="1">';
        $html .= "\n\t\t\t\t" . '<tbody>';
        foreach ($coinEmperor['references'] as $coinEmperorReference) {
            $reference = $references[$coinEmperorReference['reference_id']];
            $year = 0 < strlen($reference['year']) ? $reference['year'] : date('Y');
            $html .= "\n\t\t\t\t\t" . '<tr>';
            $html .= "\n\t\t\t\t\t\t" . '<td>' . $year . '</td>';
            $html .= "\n\t\t\t\t\t\t" . '<td>' . $reference['acronym'] . '</td>';
            $html .= "\n\t\t\t\t\t\t" . '<td>' . $coinEmperorReference['identifier'] . '</td>';
            $html .= "\n\t\t\t\t\t\t" . '<td align="left">' . $reference['title'] . '</td>';
            $html .= "\n\t\t\t\t\t\t" . '<td>' . $coinEmperorReference['page'] . '</td>';
            $html .= "\n\t\t\t\t\t" . '</tr>';
        }
        $html .= "\n\t\t\t\t" . '</tbody>';
        $html .= "\n\t\t\t" . '</table>';
        return $html;
    }

    public static function coinEmperorTable($coinEmperors, $emperors): string
    {
        return '<table width="100%" border="1" cellpadding="2" cellspacing="1">' . "\n\t" .
            '<thead>' .  self::emperorHeaderRow($emperors, 2) . "\t" . '</thead>' . "\n\t" .
            '<tbody>' .  self::coinEmperorRows($coinEmperors, $emperors) . "\t" . '</tbody>' . "\n\t"
            . '</table>' . "\n";
    }

    public static function emperorListTable($emperor, $coins, $lexicon): string
    {
        $html = "\n\t\t\t" . '<table width="100%" border="1" cellpadding="2" cellspacing="1">';
        $html .= "\n\t\t\t\t" . '<thead>';
        $html .= "\n\t\t\t\t\t" . '<tr>';
        $html .= "\n\t\t\t\t\t\t" . '<th align="left">' . $lexicon['reverse'] . '</th>';
        $html .= "\n\t\t\t\t\t\t" . '<th></th>';
        $html .= "\n\t\t\t\t\t" . '</tr>';
        $html .= "\n\t\t\t\t" . '</thead>';
        $html .= "\n\t\t\t\t" . '<tbody>';
        foreach ($coins as $coin) {
            $coinTitle = self::linkTitle($coin['href']);
            $html .= "\n\t\t\t\t\t" . '<tr>';
            $html .= "\n\t\t\t\t\t\t" . '<td><a href="' . $coin['href'] . '" title="' . $coinTitle . '">' . $coin['coin'] . '</a></td>';
            $html .= "\n\t\t\t\t\t\t" . '<td>' . $coin['count'] . '</td>';
            $html .= "\n\t\t\t\t\t" . '</tr>';
        }
        $html .= "\n\t\t\t\t" . '</tbody>';
        $html .= "\n\t\t\t" . '</table>';
        return $html;
    }

    public static function gallery($images, $maxCells, $title, $lexicon): string
    {
        $imagePreviewSize = '256';
        $baseUrl = 'http://img1.tienmyhieu.com/';
        $html = "\n\t\t\t" . '<table width="100%" border="1" cellpadding="2" cellspacing="1">';
        $html .= "\n\t\t\t\t" . '<tr>';
        $i = 0;
        foreach ($images as $image) {
            $imageTitle = self::linkTitle($title . '_' . $image['href']);
            $src =  $baseUrl . $imagePreviewSize . '/' . $image['src'];
            $imageHref = $baseUrl . '1024/' . $image['src'];
            if ($i == $maxCells) {
                $html .= "\n\t\t\t\t" . '</tr>';
                $html .= "\n\t\t\t\t" . '<tr>';
                $i = 0;
            }
            $html .= "\n\t\t\t\t\t" . '<td>';
            $html .= '<a href="' . $imageHref . '" title="' . $imageTitle . '">';
            $html .= '<img src="' . $src . '" alt="' . $imageTitle . '" /></a>';
//            if (!in_array('weight', array_keys($image))) {
//                echo '<pre>';
//                echo $image['image_id'] . PHP_EOL;
//                print_r(array_keys($image));
//                echo '</pre>';
//            }
            if (in_array('weight', array_keys($image)) && 0 < strlen($image['weight'])) {
                $html .= '<br/>' . $image['diameter'] . 'mm - ' . $image['weight'] . $lexicon['grams'] ;
            }

            $html .= '</td>';
            $i++;
        }
        $cellsRemaining = $maxCells - $i;
        if (0 < $cellsRemaining) {
            for ($i = 0; $i < $cellsRemaining; $i++) {
                $html .= "\n\t\t\t\t\t" . '<td></td>';
            }
        }
        $html .= "\n\t\t\t\t" . '</tr>';
        $html .= "\n\t\t\t" . '</table>';
        return $html;
    }

    public static function referenceEmperorRows($referenceEmperors, $emperors): string
    {
        $html = '';
        $i = 1;
        foreach ($referenceEmperors as $referenceId => $reference) {
            $html .= "\n\t" . '<tr>' . "\n\t\t" .
                '<td>' .  $i . '</td>' .
                '<td>' .  $reference['year'] . '</td>' .
                '<td>' .  $reference['acronym'] . '</td>';

            $html .= "\n\t\t" . '<td><a href="' . $reference['href'] . '" title="' . $reference['title'] . '">' .
                self::truncatedTitle($reference['title']) . '</a></td>';
            foreach($reference['emperors'] as $emperorId => $emperor) {
                $emperorTitle = self::linkTitle($emperor['href']);
                $html .= "\n\t\t" . '<td>';
                if (0 < $emperor['count']) {
                    $html .= '<a href="' . $emperor['href'] . '" title="' . $emperorTitle . '">' .
                        $emperor['count'] . '</a>';
                }
                $html .= '</td>';
            }
            $html .= "\n\t" . '</tr>';
            $i++;
        }
        return $html;
    }

    public static function referenceEmperorTable($referenceEmperors, $emperors, $lexicon): string
    {
        $title = '<h2>' . $lexicon['references'] . '</h2>';
        return $title . '<table width="100%" border="1" cellpadding="2" cellspacing="1">' . "\n\t" .
            '<thead>' .  self::emperorHeaderRow($emperors, 4) . "\t" . '</thead>' . "\n\t" .
            '<tbody>' .  self::referenceEmperorRows($referenceEmperors, $emperors) . "\t" . '</tbody>' . "\n\t"
            . '</table>' . "\n";
    }

    public static function sectionTitle($title)
    {
       return '<br/><h2>' . $title . '</h2>';
    }

    public static function twoCellEnd(): string
    {
        $html = "\n\t\t" . '</td>';
        $html .= "\n\t" . '</tr>';
        $html .= "\n" . '</table>';
        return $html;
    }

    public static function twoCellMiddle(): string
    {
        $html = "\n\t\t" . '</td>';
        $html .= "\n\t\t" . '<td valign="top">';
        return $html;
    }

    public static function twoCellStart(): string
    {
        $html = '<table valign="top">';
        $html .= "\n\t" . '<tr>';
        $html .= "\n\t\t" . '<td valign="top">';
        return $html;
    }

    private static function linkTitle($title)
    {
        return preg_replace('|_|', ' ', $title);
    }

    private static function truncatedTitle($title)
    {
        $maxLength = 70;
        $ellipsis = mb_strlen($title) >= $maxLength ? '...' : '';
        return mb_strlen($title) >= $maxLength ? mb_substr($title, 0, $maxLength) . $ellipsis : $title;
    }
}
