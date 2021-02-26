<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use adapters\HtmlOutput;
use core\Factory;

require_once (__DIR__ . '/adapters/HtmlOutput.php');
require_once (__DIR__ . '/core/Factory.php');
$factory =  new Factory();
$meta = $factory->meta();
$template = $meta->template();
$output = $factory->output();
// reference
// collection
// plate
switch ($template) {
    case 'article':
        $identifier = $meta->identifier();
        $articles = $factory->articles();
        $entities = [
            'article' => $articles->getArticle($identifier, $meta->language()),
            'lexicon' => $articles->lexicon()
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
        $coinEmperors = $factory->coinEmperors();
        $coinEmperor = $factory->coinEmperor();
        $identifier = $meta->identifier();
        $emperor = $coinEmperors->getEmperorByCoinEmperorId($identifier);
        $referenceEmperors = $factory->referenceEmperors();
        $articles = $factory->articles();
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
        $coinEmperors = $factory->coinEmperors();
        $referenceEmperors = $factory->referenceEmperors();
        $articles = $factory->articles();
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
<?php echo $output->get($meta->title(), $template); ?>
</body>
</html>

