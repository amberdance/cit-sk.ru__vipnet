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

define('ROUTES', "{$_SERVER['DOCUMENT_ROOT']}/citsk/config/routes.php");
define('JWT', [
    'iss'        => 'https://crypto-api/',
    'aud'        => 'https://crypto-api/',
    'iat'        => 1590008094,
    'nbf'        => 1590008094,
    'secret_key' => "|mIIGLfLZ*fGiY1Xlb|^PO=ZTs;SHXngSW()&dtW3:rz_vB;vb]nTP49-NbH0`0",
]);
