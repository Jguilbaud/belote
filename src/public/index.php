<?php
try {
    require_once '../conf/constants.inc.php';
    require_once PATH.'/conf/conf.inc.php';
    require_once '../conf/autoload.inc.php';
    if (DEBUG) {
        ini_set('display_errors', true);
    }

    $oRouter = new \Services\HttpRouter(PATH . '/conf/routes.json');
    $oRouter->doResponse();
} catch ( Throwable $e ) {
    echo 'Une erreur imprévue et ingérable est survenue <br />' . "\n";

    if (defined('DEBUG') && DEBUG) {
        echo $e->getMessage() . '<br />' . "\n";
        echo $e->getFile() . '(' . $e->getLine() . ')<br />' . "\n";
        echo $e->getTraceAsString();
    }
}
