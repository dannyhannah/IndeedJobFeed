<?php

require 'vendor/autoload.php';

// Set your API Publisher number
define('PUBLISHER_KEY', 3024762205542040);

$loader = new Twig_Loader_Filesystem(__DIR__.'/src/Indeed/Feed/View/');
$twig = new Twig_Environment($loader);

$method = 'defaultAction';
if (isset($_GET['search'])) {
    $method = 'getFeed';
} 

try {
    $controller = new Indeed\Feed\Controller\Feed($twig);
    $controller->$method();
} catch (\Exception $e) {
    var_dump($e);
}

