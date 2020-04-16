<?php
// Autoload
spl_autoload_register(function ($className) {

    $className = strtolower($className);
    // On détecte s'il y'a un namespace
    $matches = null;
    if (preg_match('#(.*)\\\\(.*)#is', $className, $matches)) {
        $namespace = $matches[1];
        $className = $matches[2];
    } else {
        $namespace = '';
    }

    switch (strtolower($namespace)) {
        case 'services\\unrestricters' :
            require_once SERVICES_PATH . 'hosters/' . $className . '.class.php';
            break;

        case 'scheduler\\task' :
            require_once SERVICES_PATH . '/tasks/' . $className . '.class.php';
            break;

        case 'i18n' :
            require_once LANGS_PATH . '/' . str_replace('lang', '', $className) . '.php';
            break;

        case 'services' :
            require_once SERVICES_PATH . $className . '.php';
            break;

        case 'models' :
            require_once MODELS_PATH . $className . '.php';
            break;

        case 'repositories' :
            require_once REPOSITORIES_PATH . $className . '.php';
            break;

        case 'exceptions' :
            require_once MODELS_PATH . 'exceptions.php';
            break;
        case 'controllers' :
            require_once CONTROLLERS_PATH . '/' . $className . '.php';
            break;
        default :
            if (preg_match('#controller$#is', $className)) {
                throw new \Exception("erreur chargement $namespace $className <br />");
                require_once CONTROLLERS_PATH . '/' . $className . '.php';
            } else {
                require_once SERVICES_PATH . '/' . $className . '.php';
            }
            break;
    }
    return true;
});

// On veut que les erreurs soient traitées en exception
function exception_error_handler($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
set_error_handler("exception_error_handler");

// Initialisation de la configuration d'accès à la BDD
\Services\Database::setDBConnectorConfig(Conf::$db['host'], Conf::$db['base'], Conf::$db['user'], Conf::$db['password']);
\Services\Database::getInstance()->setPdoDriver(Conf::$db['driver']);
