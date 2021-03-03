<?php
namespace adapters;

class HtmlOutput
{
    const BASE_IMG_URL = 'http://img1.tienmyhieu.com/';
    const IMG_PREVIEW_SIZE = '256';

    public static function article($article, $lexicon): string
    {
        $html = '';
        $html .= "\n\t\t\t" . '<table border="1" cellpadding="5" cellspacing="1">';
        $html .= "\n\t\t\t\t" . '<tbody>';
        $html .= "\n\t\t\t\t\t" . '<tr>';
        $html .= HtmlOutput::articleIntro($article['intro']);
        $html .= "\n\t\t\t\t\t" . '</tr>';
        foreach ($article['sections'] as $section) {
            $html .= "\n\t\t\t\t\t" . '<tr>';
            $html .= HtmlOutput::articleSection($section);
            $html .= "\n\t\t\t\t\t" . '</tr>';
        }
        $html .= "\n\t\t\t\t" . '</tbody>';
        $html .= "\n\t\t\t" . '</table>';
        return $html;
    }

    public static function articleSection($section): string
    {
        $baseImageHref = HtmlOutput::BASE_IMG_URL . 'uploads/256/';
        $html = "\n\t\t\t\t\t\t" . '<td>';
        foreach ($section['images'] as $image) {
            $imageHref = $baseImageHref . $image['src'];
            $html .= '<a href="' . $imageHref . '" title="' . $image['title'] . '">';
            $html .= '<img src="' . $imageHref . '" alt="' . $image['title'] . '" /></a>';
        }
        $html .= "\n\t\t\t\t\t\t" . '</td>';
        $html .= "\n\t\t\t\t\t\t" . '<td valign="top">';
        foreach ($section['sentences'] as $sentence) {
            $html .= $sentence . '.<br />';
        }
        $html .= "\n\t\t\t\t\t\t" . '</td>';
        return $html;
    }

    public static function articleIntro($intro): string
    {
        $isUpload = 0 < strlen($intro['image_dir']);
        $targetImgSize = $isUpload ? '256' : '1024';
        $html = "\n\t\t\t\t\t\t" . '<td>';
        foreach ($intro['images'] as $introImage) {
            $imgSrc = HtmlOutput::BASE_IMG_URL . HtmlOutput::IMG_PREVIEW_SIZE . '/' . $introImage['src'];
            $imageHref = HtmlOutput::BASE_IMG_URL . $targetImgSize . '/' . $introImage['src'];
            $imageTitle = $introImage['title'];
            $html .= '<a href="' . $imageHref . '" title="' . $imageTitle . '">';
            $html .= '<img src="' . $imgSrc . '" alt="' . $imageTitle . '" /></a>';
        }

        $html .= '</td>';
        $html .= "\n\t\t\t\t\t\t" . '<td valign="top">';
        foreach ($intro['sentences'] as $sentence) {
            $html .= $sentence . '. ';
        }
        $html .= "\n\t\t\t\t\t\t" . '</td>';
        return $html;
    }

    public static function articlesTable($articles, $lexicon): string
    {
        $title = self::sectionTitle($lexicon['articles'], 2);
        $html = $title;
        if (0 < count($articles)) {
            $html .= "\n\t\t\t" . '<table border="1" cellpadding="2" cellspacing="1">';
            $html .= "\n\t\t\t\t" . '<tbody>';
            $i = 1;
            foreach ($articles as $article) {
                $html .= "\n\t\t\t\t\t" . '<tr>';
                $html .= "\n\t\t\t\t\t\t" . '<td>' . $i . '</td>';
                $html .= "\n\t\t\t\t\t\t" . '<td>' . $article['date'] . '</td>';
                $html .= "\n\t\t\t\t\t\t" . '<td><a href="' . $article['href'] . '" title="' . $article['title'] . '">' .
                    $article['title'] . '</a></td>';
                $html .= "\n\t\t\t\t\t" . '</tr>';
                $i++;
            }
            $html .= "\n\t\t\t\t" . '</tbody>';
            $html .= "\n\t\t\t" . '</table>';
        }
        return $html;
    }

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

    public static function emperorImages($emperor, $coins, $title): string
    {
        $html = '';
        $imagePreviewSize = '256';
        $baseUrl = 'http://img1.tienmyhieu.com/';
        foreach ($emperor['emperor_images'] as $emperorImage) {
            $image = $emperor['images'][$emperorImage['image_uuid']];
            $coin = $coins[$emperorImage['coin_uuid']];
            $imageTitle = $title . ' ' . $coin['name'];
            $imageHref = preg_replace('|\s+|', '_', $title) . '_' . $coin['href'];
            $src = $baseUrl . $imagePreviewSize . '/' . $image['src'];
            $html .= '<a href="' . $imageHref . '" title="' . $imageTitle . '">';
            $html .= '<img src="' . $src . '" alt="' . $imageTitle . '" /></a>';
        }
        return $html;
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
        $title = self::sectionTitle($lexicon['private_collections'], 3);
        $html = $title;
        if (0 < count($collection)) {
            $html .= "\n\t\t\t" . '<table width="100%" border="1" cellpadding="2" cellspacing="1">';
            $html .= "\n\t\t\t\t" . '<tbody>';
            foreach ($collection as $item) {
                $hasObverse = 0 < strlen($item['url_obverse']);
                $hasReverse = 0 < strlen($item['url_reverse']);
                $obvUrl = $hasObverse ? '<a href="' . $item['url_obverse'] . '" target="_blank">' . $lexicon['obverse'] . '</a>' : '';
                $revUrl = $hasReverse ? '<a href="' . $item['url_reverse'] . '" target="_blank">' . $lexicon['reverse'] . '</a>' : '';
                $thisYear =  date('Y');
                $source = $sources[$item['collection_uuid']];
                $year = 0 < strlen($source['year']) ? $source['year'] : $thisYear;
                $year = preg_replace('|9999|', $thisYear, $year);
                $html .= "\n\t\t\t\t\t" . '<tr>';
                $html .= "\n\t\t\t\t\t\t" . '<td>' . $item['date'] . '</td>';
                $html .= "\n\t\t\t\t\t\t" . '<td>' . $source['acronym'] . '</td>';
                $html .= "\n\t\t\t\t\t\t" . '<td align="left">' . $source['title'] . '</td>';
                $html .= "\n\t\t\t\t\t\t" . '<td>' . $obvUrl . '</td>';
                $html .= "\n\t\t\t\t\t\t" . '<td>' . $revUrl . '</td>';
                $html .= "\n\t\t\t\t\t" . '</tr>';
            }
            $html .= "\n\t\t\t\t" . '</tbody>';
            $html .= "\n\t\t\t" . '</table>';
        } else {
            $html .= '&nbsp;&nbsp;&nbsp;' . $lexicon['none'] . '<br />';
        }
        return $html;
    }

    public static function references($lexicon, $entityReferences, $references, $titleSuffix=''): string
    {
        $title = self::sectionTitle($lexicon['references'] . $titleSuffix, 3);
        $html = $title;
        if (0 < count($entityReferences)) {
            $html .= "\n\t\t\t" . '<table width="100%" border="1" cellpadding="2" cellspacing="1">';
            $html .= self::referenceTableHeader($lexicon);
            $html .= "\n\t\t\t\t" . '<tbody>';
            $i = 1;
            foreach ($entityReferences as $entityReference) {
                $reference = $references[$entityReference['reference_uuid']];
                $html .= self::referenceTableRow($entityReference, $reference, [], $lexicon, $i);
                $i++;
            }
            $html .= "\n\t\t\t\t" . '</tbody>';
            $html .= "\n\t\t\t" . '</table>';
        } else {
            $html .= '&nbsp;&nbsp;&nbsp;' . $lexicon['none'] . '<br />';
        }
        return $html;
    }

    public static function referenceTableRow($entityReference, $reference, $articles, $lexicon, $i): string
    {
        $year = 0 < strlen($reference['year']) ? $reference['year'] : date('Y');
        $html = "\n\t\t\t\t\t" . '<tr>';
        $html .= "\n\t\t\t\t\t\t" . '<td valign="top">' . $i . '</td>';
        $html .= "\n\t\t\t\t\t\t" . '<td valign="top">' . $year . '</td>';
        $html .= "\n\t\t\t\t\t\t" . '<td valign="top">' . $reference['author'] . '</td>';
        $html .= "\n\t\t\t\t\t\t" . '<td valign="top">' . $entityReference['identifier'] . '</td>';
        $html .= "\n\t\t\t\t\t\t" . '<td align="left" valign="top">' . self::truncatedTitle($reference['title']);
        if (0 < strlen($entityReference['notes'])) {
            $html .= '<br>' . "&nbsp;&nbsp;-&nbsp;" . '<small>' . $lexicon[$entityReference['notes']] . '</small>';
        }
        if (0 < count($entityReference['articles'])) {
            foreach ($entityReference['articles'] as $articleUuid) {
                $article = $articles[$articleUuid];
                $articleLink = '<a href="' . $article['href'] . '" title="' . $article['title'] . '">' . $article['title'] . '</a>';
                $html .= '<br>' . "&nbsp;&nbsp;-&nbsp;" . '<small>' . $lexicon['articles'] . ': ' . $articleLink . '</small>';
            }
        }
        $html .= '</td>';
        $html .= "\n\t\t\t\t\t\t" . '<td valign="top">' . $entityReference['page'] . '</td>';
        $html .= "\n\t\t\t\t\t" . '</tr>';
        return $html;
    }

    public static function referenceTableHeader($lexicon)
    {
        $html = "\n\t\t\t\t" . '<thead>';
        $html .= "\n\t\t\t\t\t" . '<tr>';
        $html .= "\n\t\t\t\t\t\t" . '<th></th>';
        $html .= "\n\t\t\t\t\t\t" . '<th>' . $lexicon['year'] . '</th>';
        $html .= "\n\t\t\t\t\t\t" . '<th align="left">' . $lexicon['author'] . '</th>';
        $html .= "\n\t\t\t\t\t\t" . '<th>' . $lexicon['number_abbrev'] . '</th>';
        $html .= "\n\t\t\t\t\t\t" . '<th align="left">' . $lexicon['references'] . '</th>';
        $html .= "\n\t\t\t\t\t\t" . '<th>' . $lexicon['page'] . '</th>';
        $html .= "\n\t\t\t\t\t" . '</tr>';
        $html .= "\n\t\t\t\t" . '</thead>';
        return $html;
    }

    public static function coinEmperorReferences($coinEmperor, $references, $lexicon, $articles): string
    {
        $title = self::sectionTitle($lexicon['references'], 3);
        $html = $title;
        if (0 < count($coinEmperor['references'])) {
            $html .= "\n\t\t\t" . '<table width="100%" border="1" cellpadding="2" cellspacing="1">';
            $html .= self::referenceTableHeader($lexicon);
            $html .= "\n\t\t\t\t" . '<tbody>';
            $i = 1;
            foreach ($coinEmperor['references'] as $coinEmperorReference) {
                $reference = $references[$coinEmperorReference['reference_uuid']];
                $html .= self::referenceTableRow($coinEmperorReference, $reference, $articles, $lexicon, $i);
                $i++;
            }
            $html .= "\n\t\t\t\t" . '</tbody>';
            $html .= "\n\t\t\t" . '</table>';
        } else {
            $html .= '&nbsp;&nbsp;&nbsp;' . $lexicon['none'] . '<br />';
        }
        return $html;
    }

    public static function coinEmperorTable($coinEmperors, $emperors): string
    {
        return '<table width="100%" border="1" cellpadding="2" cellspacing="1">' . "\n\t" .
            '<thead>' .  self::emperorHeaderRow($emperors, 2) . "\t" . '</thead>' . "\n\t" .
            '<tbody>' .  self::coinEmperorRows($coinEmperors, $emperors) . "\t" . '</tbody>' . "\n\t"
            . '</table>' . "\n";
    }

    public static function coinListTable($coins, $lexicon): string
    {
        $html = "\n\t\t\t" . '<table width="100%" border="1" cellpadding="2" cellspacing="1">';
        $html .= "\n\t\t\t\t" . '<thead>';
        $html .= "\n\t\t\t\t\t" . '<tr>';
        $html .= "\n\t\t\t\t\t\t" . '<th align="left">' . $lexicon['reverse'] . '</th>';
        $html .= "\n\t\t\t\t\t" . '</tr>';
        $html .= "\n\t\t\t\t" . '</thead>';
        $html .= "\n\t\t\t\t" . '<tbody>';
        foreach ($coins as $coin) {
            $coinTitle = self::linkTitle($coin['href']);
            $html .= "\n\t\t\t\t\t" . '<tr>';
            $html .= "\n\t\t\t\t\t\t" . '<td nowrap="nowrap"><a href="' . $coin['href'] . '" title="' . $coinTitle . '">' . $coin['name'] . '</a></td>';
            $html .= "\n\t\t\t\t\t" . '</tr>';
        }
        $html .= "\n\t\t\t\t" . '</tbody>';
        $html .= "\n\t\t\t" . '</table>';
        return $html;
    }

    public static function maximsTable($coin, $descriptions, $references, $lexicon)
    {
        $html = '';
        if (in_array('reference_maxims', array_keys($descriptions))) {
            if (0 < count($descriptions['reference_maxims'])) {
                $maxims = [];
                foreach ($descriptions['maxims'] as $maxim) {
                    $maxims[$maxim['maxim_uuid']] = $maxim['maxim'];
                }
                $descriptions['maxims'] = $maxims;
                $html .= HtmlOutput::sectionTitle($lexicon['meaning'], 3, false);
                $html .= "\n\t\t\t" . '<table cellpadding="2" cellspacing="1">';
                $i = 1;
                foreach ($descriptions['reference_maxims'] as $maximUuid => $maximReferences) {
                    $maxim = $maxims[$maximUuid];
                    $html .= "\n\t\t\t\t" . '<tr>';
                    $html .= "\n\t\t\t\t\t" . '<td valign="top">' . $i . '</td>';
                    $html .= "\n\t\t\t\t\t" . '<td valign="top" colspan="3">';
                    $html .= '<b>' . $maxim . '</b>';
                    $html .= "\n\t\t\t\t\t" . '</td>';
                    $html .= "\n\t\t\t\t" . '</tr>';
                    foreach ($maximReferences as $maximReference) {
                        $reference = $references[$maximReference['reference_uuid']];
                        $html .= "\n\t\t\t\t" . '<tr>';
                        $html .= "\n\t\t\t\t\t" . '<td>&nbsp;</td>';
                        $html .= "\n\t\t\t\t\t" . '<td valign="top">' . $reference['year'] . '</td>';
                        $html .= "\n\t\t\t\t\t" . '<td valign="top">' . self::truncatedTitle($reference['title'], 50) . '</td>';
                        $html .= "\n\t\t\t\t\t" . '<td valign="top">' . $maximReference['page'] . '</td>';
                        $html .= "\n\t\t\t\t\t" . '</td>';
                        $html .= "\n\t\t\t\t" . '</tr>';

                    }
                    $html .= "\n\t\t\t\t" . '<tr><td colspan="4">&nbsp;</td></tr>';
                    $i++;
                }
                $html .= "\n\t\t\t" . '</table>';
            }
        }
        return $html;
    }

    public static function coinEmperors($coin, $emperors, $inscriptions): string
    {
        $html = '';
        $imagePreviewSize = '128';
        $baseUrl = 'http://img1.tienmyhieu.com/';
        if (in_array('emperors', array_keys($coin))) {
            foreach ($coin['emperors'] as $emperorCoin) {
                $emperor = $emperors[$emperorCoin['emperor_uuid']];
                $emperorTitle = self::linkTitle($emperor['href']);
                $html .= "\n\t\t\t" . '<table border="1" cellpadding="2" cellspacing="1">';
                $html .= "\n\t\t\t\t" . '<tr>';
                $html .= "\n\t\t\t\t\t" . '<td valign="top" colspan="2">';
                $html .= "\n\t\t\t\t\t" . $emperorTitle;
                $html .= "\n\t\t\t\t\t" . '</td>';
                $html .= "\n\t\t\t\t" . '</tr>';
                $html .= "\n\t\t\t\t" . '<tr>';
                if (1 == count($emperorCoin['images'])) {
                    $html .= "\n\t\t\t\t\t" . '<td valign="top">&nbsp;</td>';
                }
                $imageTitle = $emperorTitle . ' ' . $coin['title'];
                foreach ($emperorCoin['images'] as $image) {
                    $image = $coin['images'][$image];
                    $src = $baseUrl . $imagePreviewSize . '/' . $image['src'];
                    $imageHref = $baseUrl . '1024/' . $image['src'];
                    $imageHref = $emperor['href'] . '_' . preg_replace('|\s+|', '_', $coin['title']) ;

                    $html .= "\n\t\t\t\t\t" . '<td valign="top">';
                    $html .= '<a href="' . $imageHref . '" title="' . $imageTitle . '">';
                    $html .= '<img src="' . $src . '" alt="' . $imageTitle . '" /></a>';
                    $html .= "\n\t\t\t\t\t" . '</td>';
                }
                $obverseInscription = $inscriptions[$emperor['inscription_uuid']]['inscription'];
                $reverseInscription = $inscriptions[$emperorCoin['inscription_uuid']]['inscription'];
                $html .= "\n\t\t\t\t" . '</tr>';
                $html .= "\n\t\t\t\t" . '<tr>';
                $html .= "\n\t\t\t\t\t" . '<td valign="top">' . $obverseInscription . '</td>';
                $html .= "\n\t\t\t\t\t" . '<td valign="top">' . $reverseInscription . '</td>';
                $html .= "\n\t\t\t\t" . '</tr>';
                $html .= "\n\t\t\t" . '</table>';
                $html .= "\n\t\t\t" . '</br>';
            }
        }
        return $html;
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
            $html .= "\n\t\t\t\t\t\t" . '<td nowrap="nowrap"><a href="' . $coin['href'] . '" title="' . $coinTitle . '">' . $coin['coin'] . '</a></td>';
            $html .= "\n\t\t\t\t\t\t" . '<td>' . $coin['count'] . '</td>';
            $html .= "\n\t\t\t\t\t" . '</tr>';
        }
        $html .= "\n\t\t\t\t" . '</tbody>';
        $html .= "\n\t\t\t" . '</table>';
        return $html;
    }

    public static function coinEmperorReferencesList($references, $images, $lexicon, $articles, $variants, $inscriptions, $title, $hasVariants): string
    {
        $html = '';
        if (0 < count($images)) {
            //$inscriptions = (1 <  count($images) ? $inscriptions['obverse'] . ' ' : '') . $inscriptions['reverse'];
            $imagePreviewSize = '128';
            $baseUrl = 'http://img1.tienmyhieu.com/';
            //$html .= '<h4>' . $inscriptions . '</h4>';
            $html .= "\n\t\t\t" . '<table border="1" cellpadding="2" cellspacing="1">';
            $i = 1;
            $withVariants = 0;
            foreach ($references as $reference) {
                $hasArticle = in_array('articles', array_keys($reference)) && 0 < count($reference['articles']);
                $hasPage = 0 < strlen($reference['page']);
                $hasPlate = 0 < strlen($reference['plate']);
                $hasMeasurements = 0 < strlen($reference['diameter']) || 0 < strlen($reference['weight']);
                if (0 < strlen($reference['variant'])) {
                    $withVariants++;
                }
                $html .= "\n\t\t\t\t" . '<tr>';
                $html .= "\n\t\t\t\t\t" . '<td valign="top">';
                $html .= $i . '. ' . $reference['code'] . '<br/>';
                if ($hasPage) {
                    $html .= '&nbsp;&nbsp;&nbsp;' . $lexicon['page'] . ': ' . $reference['page'] . '<br/>';
                }
                if ($hasPlate) {
                    $html .= '&nbsp;&nbsp;&nbsp;' . $lexicon['plate'] . ': ' . $reference['plate'] . '<br/>';
                }
                if ($hasMeasurements) {
                    $html .= '&nbsp;&nbsp;&nbsp;' . $lexicon['diameter'] . ': ' . $reference['diameter'] . '<br/>';
                    $html .= '&nbsp;&nbsp;&nbsp;' . $lexicon['weight'] . ': ' . $reference['weight'] . '<br/>';
                }
                if ($hasArticle) {
                    $linksHtml = '&nbsp;&nbsp;&nbsp;' . $lexicon['articles'] . ': ';
                    $i = 1;
                    foreach ($reference['articles'] as $articleUuid) {
                        $article = $articles[$articleUuid];
                        $linksHtml .= '<a href="' . $article['href'] . '" title="' . $article['title'] . '">' . $i . '</a>, ';
                        $i++;
                    }
                    $linksHtml = substr($linksHtml, 0,  -2) . '<br/>';
                    $html .= $linksHtml;
                }
//                if ($hasVariant) {
//                    $variant = $variants[$reference['variant']]['variant'];
//                    $html .= '&nbsp;&nbsp;&nbsp;' . $lexicon['variant'] . ': ' . $variant . '<br/>';
//                }
                $html .= '</td>';
                $html .= "\n\t\t\t\t\t" . '<td align="right">';
                $imageTitle = $reference['code'];
                foreach ($reference['images'] as $imageUuid) {
                    $image = $images[$imageUuid];
                    $src =  $baseUrl . $imagePreviewSize . '/' . $image['src'];
                    $imageHref = $baseUrl . '1024/' . $image['src'];
                    $html .= '<a href="' . $imageHref . '" title="' . $title . '">';
                    $html .= '<img src="' . $src . '" alt="' . $title . '" /></a>';
                }
                $html .= "\n\t\t\t\t\t" . '</td>';
                $html .= "\n\t\t\t\t" . '</tr>';
                $i++;
            }
            $html .= "\n\t\t\t" . '</table>';
            if ($hasVariants) {
                $html .= '<br /> *' . $lexicon['dot_thong_variants'] . '<br />';
            }
        } else {
            $html .= '&nbsp;&nbsp;&nbsp;' . $lexicon['none'] . '<br />';
        }
        return $html;
    }

    public static function gallery($images, $maxCells, $title, $lexicon): string
    {
        $imagePreviewSize = '128';
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
                '<td>' .  $reference['year'] . '</td>';
//                '<td>' .  $reference['acronym'] . '</td>';

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
            '<thead>' .  self::emperorHeaderRow($emperors, 3) . "\t" . '</thead>' . "\n\t" .
            '<tbody>' .  self::referenceEmperorRows($referenceEmperors, $emperors) . "\t" . '</tbody>' . "\n\t"
            . '</table>' . "\n";
    }

    public static function sectionTitle($title, $size, $withBr=true)
    {
       return ($withBr ? '<br/>': '') . '<h' . $size . ' style="margin-top: 0">' . $title . '</h' . $size . '>';
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

    public static function bibliography($bibliographyFields)
    {
        $html = '';
        if (0 < count($bibliographyFields)) {
            $html .= '<br />';
            $html .= "\n\t\t\t" . '<table border="1" cellpadding="2" cellspacing="2">';
            foreach ($bibliographyFields as $key => $value) {
                if (is_array($value)) {
                    $value = implode(', ', $value);
                }
                $html .= "\n\t\t\t\t" . '<tr>';
                $html .= "\n\t\t\t\t\t" . '<td>' . $key . '</td>';
                $html .= "\n\t\t\t\t\t" . '<td>' . $value . '</td>';
                $html .= "\n\t\t\t\t" . '</tr>';
            }
            $html .= "\n\t\t\t" . '</table>';
            $html .= '<br />';
        }
        return $html;
    }

    public static function reference($reference, $lexicon): string
    {
        $html = '';
        $html .= HtmlOutput::bibliography($reference['bibliography']);
        foreach ($reference['collections'] as $collection) {
            if ((bool)$collection['expand']) {
                if (0 < strlen($collection['title'])) {
                    $collectionCount = 0 < $collection['items'] ? ' (' . $collection['items'] . ')' : '';
                    $title = preg_replace('|_|', ' ',  $collection['title']);
                    $href = preg_replace('|\s+|', '_', $title);
                    $html .= '<a href="' . $href . '" title="' . $title . '">';
                    $html .= $title . '</a>' . $collectionCount . '<br /><br />';
                } else {
                    $expandedCollection = $reference['expanded_collections'][$collection['uuid']];
                    foreach ($expandedCollection['collections'] as $subLevelCollection) {
                        $hasIdentifier = 0 < strlen($subLevelCollection['identifier']);
                        $identifier = $hasIdentifier ? $subLevelCollection['identifier'] . '. ' : '';
                        $hasSpecimens = in_array('specimens', array_keys($expandedCollection));
                        $specimens = $hasSpecimens ? $expandedCollection['specimens'] : [];
                        $html .= $identifier . $subLevelCollection['title'] . '<br />';
                        $html .= HtmlOutput::collectionTable(
                            $subLevelCollection,
                            $expandedCollection['images'],
                            $expandedCollection['original_images'],
                            $specimens,
                            $lexicon,
                            '',
                            '64'
                        );
                    }
                }
            } else {
                $hasSpecimens = in_array('specimens', array_keys($collection));
                $specimens = $hasSpecimens ? $collection['specimens'] : [];
                $html .= HtmlOutput::collectionTable(
                    $collection,
                    $reference['images'],
                    $reference['original_images'],
                    $specimens,
                    $lexicon,
                    'uploads',
                    '128'
                );
            }
        }
        if (in_array('videos', array_keys($reference))) {
            foreach ($reference['videos'] as $video) {
                $html .= HtmlOutput::embeddedVideo($video);
            }
        }
        return $html;
    }

    public static function collectionTable($collection, $images, $originalImages, $specimens, $lexicon, $baseDir, $imgSize): string
    {
        $imagePreviewSize = '256';
        $baseUrl = 'http://img1.tienmyhieu.com/';
        $html = "\n\t\t\t" . '<table border="1" cellpadding="0" cellspacing="0">';
        $html .= "\n\t\t\t\t" . '<tr>';
        foreach ($collection['images'] as $uuid) {
            $image = $images[$uuid];
            $baseDir .= (0 < strlen($baseDir) ? '/' : '');
            $imgSrc = $baseUrl . $baseDir . $imgSize . '/' . $image['src'];
            $aHref = $baseUrl . $baseDir . '1024/' . $image['src'];
            $imgTitle = preg_replace('|_|', ' ', $image['href']);
            if (in_array('title', array_keys($image))) {
                if (0 < strlen($image['title'])) {
                    $imgTitle = $image['title'];
                }
            }
            if (in_array($uuid, array_keys($originalImages))) {
                $originalImage = $originalImages[$uuid];
                $aHref = $baseUrl . 'uploads/' . $originalImage['src'];
                $imgSrc = $baseUrl . 'uploads/' . $imgSize . '/' . $image['src'];
            }
            if ($image['href'] !== 'NONE') {
                $html .= "\n\t\t\t\t\t" . '<td>' . HtmlOutput::linkedImage($aHref, $imgTitle, $imgSrc, $imgTitle) . '</td>';
            } else {
                $html .= "\n\t\t\t\t\t" . '<td>' . HtmlOutput::notLinkedImage($aHref, $imgTitle, $imgSrc, $imgTitle) . '</td>';
            }

        }
        if (in_array('specimen_uuid', array_keys($collection))) {
            if (0 < strlen($collection['specimen_uuid'])) {
                $specimen = $specimens[$collection['specimen_uuid']];
                $hasMeasurements = 0 < strlen($specimen['diameter']) || 0 < strlen($specimen['weight']);
                $html .= "\n\t\t\t\t\t" . '<td valign="top">';
                if ($hasMeasurements) {
                    $html .= $lexicon['diameter'] . ': ' . $specimen['diameter'] . '<br/>';
                    $html .= $lexicon['weight'] . ': ' . $specimen['weight'] . '<br/>';
                }
                $html .= "\n\t\t\t\t\t" . '</td>';
            }
        }
        $html .= "\n\t\t\t\t" . '</tr>';
        $html .= "\n\t\t\t\t" . '</table>';
        $html .= "\n\t\t\t\t" . '<br />';
        return $html;
    }

    public static function embeddedVideo($video)
    {
        $embedHref = 'https://www.youtube.com/embed/' . $video['uuid'] . '?hl=' . $video['locale'];
        $href = 'https://www.youtube.com/watch?v=' . $video['uuid'];
        if (0 < $video['start']) {
            $href .= '&t=' . $video['start'] . 's';
        }
        $html = '<a href="' . $href . '" target="_blank" style="position:relative; display:inline-block;">';
        $html .= '<div style="position:absolute; height:100%; width:100%; z-index:1;"></div>';
        $html .= '<iframe style="z-index: 2;" width="600" height="400" src="' . $embedHref . '" frameborder="0" allowfullscreen></iframe>';
        $html .= '</a>';
        return $html;
    }

    public static function notLinkedImage($aHref, $aTitle, $imgSrc, $imgTitle): string
    {
        return '<img src="' . $imgSrc . '" alt="' . $imgTitle . '" style="display: block; padding: 0; margin: 0" />';
    }

    public static function linkedImage($aHref, $aTitle, $imgSrc, $imgTitle): string
    {
        $html = '<a href="' . $aHref . '" title="' . $aTitle . '">';
        $html .= '<img src="' . $imgSrc . '" alt="' . $imgTitle . '" style="display: block; padding: 0; margin: 0" /></a>';
        return $html;
    }

    public static function collectionLevel($collection, $uuid, $coins, $lexicon)
    {
        $html = '';
        $continue = !$collection['attributes']['lowest_level'];
        if ($continue) {
            foreach ($collection['collections'] as $subLevelUUid => $subLevelCollection) {
                $html .= HtmlOutput::collectionLevel($subLevelCollection, $subLevelUUid, $coins, $lexicon);
            }
        } else {
            foreach ($collection['collections'] as $subLevelUUid => $subLevelCollection) {
                if (0 < count($subLevelCollection['images'])) {
                    $coinUuid = $collection['attributes']['coin_uuid'];
                    if ($coinUuid) {
                        $hasIdentifier = 0 < strlen($subLevelCollection['identifier']);
                        $identifer = $hasIdentifier ? $subLevelCollection['identifier'] . '. ' : '';
                        $coin = $coins[$coinUuid]['name'];
                        $html .=  $identifer . $coin;
                    }
                    $hasSpecimens = in_array('specimens', array_keys($subLevelCollection));
                    $specimens = $hasSpecimens ? $subLevelCollection['specimens'] : [];
                    $html .= HtmlOutput::collectionTable(
                        $subLevelCollection,
                        $collection['images'],
                        $collection['original_images'],
                        $specimens,
                        $lexicon,
                        '',
                        '64'
                    );
                }
            }
        }
        return $html;
    }

    public static function collection($collection, $coins, $lexicon)
    {
        $html = '';
        foreach ($collection['collections'] as $levelOneUUid => $levelOneCollection) {
            $html .= HtmlOutput::collectionLevel($levelOneCollection, $levelOneUUid, $coins, $lexicon);
        }
        return $html;
    }

    private static function linkTitle($title)
    {
        return preg_replace('|_|', ' ', $title);
    }

    private static function truncatedTitle($title, $maxLength=50): string
    {
        $ellipsis = mb_strlen($title) >= $maxLength ? '...' : '';
        return mb_strlen($title) >= $maxLength ? mb_substr($title, 0, $maxLength) . $ellipsis : $title;
    }
}
