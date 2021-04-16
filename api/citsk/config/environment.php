<?php

/**
 * @param string $mode
 *
 * @return void
 */
function setEnvironmentMode(string $mode): void
{
    if (is_numeric(strpos($mode, "prod"))) {
        define('DB_DEBUG', false);
    }

    if (is_numeric(strpos($mode, "dev"))) {
        define('DB_DEBUG', true);
    }
}

define('ROUTES', "{$_SERVER['DOCUMENT_ROOT']}/api/citsk/config/routes.php");
define('JWT', [
    'iss'        => 'http://cryptoapps.cit-sk.ru/',
    'aud'        => 'http://cryptoapps.cit-sk.ru/',
    'iat'        => 1590008094,
    'nbf'        => 1590008094,
    'secret_key' => "|mIIGLfLZ*fGiY1Xlb|^PO=ZTs;zSHXngSW()&dtW3:rz_vB;vb]nTP49-NbH0`0",
]);
