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
    case 'coin':
        $identifier = $meta->identifier();
        $coin = $factory->coin();
        $entities = [
            'coin' => $coin->get($identifier),
            'coins' => $coin->coins(),
            'emperors' => $coin->emperors(),
            'inscriptions' => $coin->getInscriptions(),
            'lexicon' => $coin->lexicon(),
            'descriptions' => $coin->descriptions($identifier),
            'references' => $coin->references(),
            'title' => $meta->title(),
        ];
        $output->setEntities($entities);
        break;
    case 'coin_emperor':
        $coinEmperor = $factory->coinEmperor();
        $identifier = $meta->identifier();
        $emperor = $coinEmperors->getEmperorByCoinEmperorId($identifier);
        $entities = [
            'coinEmperor' => $coinEmperor->get($identifier, 'ca'),
            'coins' => $coinEmperors->getEmperorList($emperor['uuid']),
            'collections' => $coinEmperors->collections(),
            'emperor' => $emperor,
            'lexicon' => $referenceEmperors->lexicon(),
            'referenceEmperors' => $referenceEmperors->get(),
            'title' => $meta->title(),
            'articles' => $articles->get(),
            'variants' => $coinEmperors->variants(),
        ];
        $output->setEntities($entities);
        break;
    case 'collection':
        $collection = $factory->collection();
        $entities = [
            'coins' => $collection->coins(),
            'lexicon' => $collection->lexicon(),
            'collection' => $collection->get($meta->identifier()),
        ];
        $output->setEntities($entities);
        break;
    case 'emperor':
        $emperor = $factory->emperor();
        $entities = [
            'coins' => $emperor->coins(),
            'emperor' => $emperor->get($meta->identifier()),
            'lexicon' => $emperor->lexicon(),
            'title' => $meta->title(),
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
    case 'reference':
        $reference = $factory->reference();
        $entities = [
            'coinEmperors' => $coinEmperors->get(),
            'lexicon' => $reference->lexicon(),
            'reference' => $reference->get($meta->identifier()),
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

