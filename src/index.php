<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use adapters\HtmlOutput;
use core\Factory;

require_once (__DIR__ . '/adapters/HtmlOutput.php');
require_once (__DIR__ . '/core/Factory.php');

$factory =  new Factory();
$articles = $factory->articles();
$coinEmperors = $factory->coinEmperors();
$meta = $factory->meta();
$referenceEmperors = $factory->referenceEmperors();
$template = $meta->template();
$output = $factory->output();
// reference
// collection
// plate
switch ($template) {
    case 'article':
        $identifier = $meta->identifier();
        $entities = [
            'article' => $articles->getArticle($identifier, $meta->language()),
            'lexicon' => $referenceEmperors->lexicon()
        ];
        $output->setEntities($entities);
        break;
    case 'coin_emperor':
        $coinEmperor = $factory->coinEmperor();
        $identifier = $meta->identifier();
        $emperor = $coinEmperors->getEmperorByCoinEmperorId($meta->identifier());
        $entities = [
            'coinEmperor' => $coinEmperor->get($identifier, 'ca'),
            'coins' => $coinEmperors->getEmperorList($emperor['uuid']),
            'collections' => $coinEmperors->collections(),
            'emperor' => $emperor,
            'lexicon' => $referenceEmperors->lexicon(),
            'referenceEmperors' => $referenceEmperors->get(),
            'title' => $meta->title(),
            'articles' => $articles->get()
        ];
        $output->setEntities($entities);
        break;
    case 'emperor':
        $emperor = $coinEmperors->getEmperorByName($meta->title());
        $entities = [
            'coins' => $coinEmperors->getEmperorList($emperor['uuid']),
            'emperor' => $emperor,
            'lexicon' => $referenceEmperors->lexicon()
        ];
        $output->setEntities($entities);
        break;
    case 'home':
        $entities = [
            'articles' => $articles->get(),
            'coinEmperors' => $coinEmperors->get(),
            'emperors' => $coinEmperors->emperors(),
            'lexicon' => $referenceEmperors->lexicon(),
            'referenceEmperors' => $referenceEmperors->get()
        ];
        $output->setEntities($entities);
    break;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $meta->language(); ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo $meta->title(); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
<body bgcolor="#FFFFFF">
<h2><?php echo $meta->title(); ?></h2>
<?php echo $output->get($template); ?>
</body>
</html>

