<?php

use Citsk\Controllers\Router;

require $_SERVER['DOCUMENT_ROOT'] . "/api/vendor/autoload.php";

setEnvironmentMode('dev');

(new Router)->setHTTPHeaders()->initializeRouting();
