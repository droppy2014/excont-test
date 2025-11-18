<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);

require __DIR__ . '/vendor/autoload.php';

use App\VedCreator\VedCreator;

$parser = new VedCreator();
$result = $parser->run("./iz.xml", "./dt.xml");

// красивый вывод результата
echo '<pre>';
print_r($result);
echo '</pre>';
