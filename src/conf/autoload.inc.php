<?php
// Autoload
require_once PATH.'/entities/exceptions.php';
// Autoloader de composer
require_once PATH.'/vendor/autoload.php';


// On veut que les erreurs soient traitées en exception
function exception_error_handler($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
set_error_handler("exception_error_handler");

// Initialisation de la configuration d'accès à la BDD
\Services\Database::setDBConnectorConfig(Conf::$db['host'], Conf::$db['base'], Conf::$db['user'], Conf::$db['password']);
\Services\Database::get()->setPdoDriver(Conf::$db['driver']);
