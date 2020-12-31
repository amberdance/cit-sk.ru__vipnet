<?php

use Citsk\Controllers\Router;

require $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";

setEnvironmentMode('dev');

$API = new Router;
$API->setHTTPHeaders()
    ->initializeRouting();
