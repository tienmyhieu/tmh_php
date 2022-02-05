<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

use tmh_core\TmhFactory;

require_once (__DIR__ . '/tmh_defines.php');
require_once (__DIR__ . '/tmh_core/TmhFactory.php');
$tmhFactory =  new TmhFactory();
$tmhController = $tmhFactory->controller();
echo $tmhController->run();
